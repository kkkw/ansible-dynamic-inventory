<?php

namespace Kkkw;

 /**
 *
 */
class AnsibleDynamicInventoryConfigParser
{

    private $files = array();
    private $hosts = array();

    public function __construct($config = '')
    {
        $this->files = $config['files'];
    }

    public function parse()
    {
        foreach ($this->files as $file) {
            $path = $file['path'];
            if (!file_exists($path)) {
                continue;
            }
            require $path;
            foreach ($config['Hostname'] as $group => $hosts) {
                foreach ($hosts as $fqdn) {
                    $this->hosts[$fqdn] = array(
                        'fqdn' => $fqdn,
                        'group' => $group,
                        'vars' => array('foo' => 'bar'),
                    );
                }
            }
            unset($config);
        }
    }

    public function show($fqdn = '')
    {
        if (!array_key_exists($fqdn, $this->hosts)) {
            return array();
        }
        return $this->hosts[$fqdn]['vars'];
    }

    public function hosts()
    {
        return $this->hosts;
    }
}
