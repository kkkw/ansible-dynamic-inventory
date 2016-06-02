<?php
namespace Kkkw;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;

/**
 *
 */
class AnsibleDynamicInventoryCommand extends Command
{

    const CONFIG_FILE = 'ansible-dynamic-inventory.config.php';

    /**
     * @inheritdoc
     */
    protected $name = 'ansible-dynamic-inventory';

    /**
     * @var array | null
     */
    protected $groups = null;

    protected $parser = null;

    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct();

        try {
            $config = $this->getConfig();
        } catch (\Exception $e) {
            echo $e->getMessage() . PHP_EOL;
            exit(1);
        }
        $this->parser = new AnsibleDynamicInventoryConfigParser($config);
        $this->parser->parse();
    }

    protected function getConfig()
    {
        $config = array();
        if (file_exists(self::CONFIG_FILE)) {
            return require(self::CONFIG_FILE);
        } else {
            throw new \Exception('No configuration file was found!');
        }
    }

    protected function getOptions()
    {
        return array(
            array('host','o', InputOption::VALUE_REQUIRED,'Show a host',null),
            array('list','l',InputOption::VALUE_NONE,'List hosts by group'),
            array('list-groups','g',InputOption::VALUE_NONE,'List groups'),
        );
    }

    public function fire()
    {
        if ($this->option('host')) {
            $host = $this->parser->show($this->option('host'));
            $this->output($host);
        } else {
            $this->listHosts();
        }
    }

    /**
     * @param string $fqdn
     */
    protected function showHost($fqdn)
    {
        $host = $this->parser->show($fqdn);
        $this->output($host);
    }

    protected function listHosts()
    {
        if ($this->option('list-groups')) {
            $this->listGroups();
        }

        $output = array();
        $hosts = $this->parser->hosts();
        foreach ($hosts as $fqdn => $host) {
            $group = $host['group'];
            if (!array_key_exists($group, $output)) {
                $output[$group] = array();
            }
            $output[$group][] = $fqdn;
        }
        $this->output(array_reverse($output));

    }

    protected function listGroups()
    {
        $hosts = $this->parser->hosts();
        $groups_tmp = array_map(function ($host) {
            return $host['group'];
        }, $hosts);
        $groups = array_reverse(array_unique($groups_tmp));
        foreach ($groups as $group) {
            $this->line($group);
        }
        exit(0);
    }

    protected function output($output)
    {
        $this->line(json_encode($output));
    }
}
