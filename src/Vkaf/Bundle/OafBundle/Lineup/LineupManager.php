<?php

namespace Vkaf\Bundle\OafBundle\Lineup;

use DateTime;
use RuntimeException;

class LineupManager
{
    private $path;
    private $cache;

    public function __construct($path)
    {
        $this->path = $path;
    }

    public function get()
    {
        if ($this->cache === null && is_readable($this->path)) {
            $json = json_decode(file_get_contents($this->path), true);
            $this->cache = [];
            foreach ($json as $row) {
                $row['begin'] = new DateTime($row['begin']);
                $row['end'] = new DateTime($row['end']);
                $this->cache[] = $row;
            }
            uasort($this->cache, function ($a, $b) {
                return $a['begin']->getTimestamp() - $b['begin']->getTimestamp();
            });
        }

        return $this->cache;
    }

    public function import(array $data)
    {
        $dir = dirname($this->path);
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }

        if (file_put_contents($this->path, json_encode($data)) === false) {
            throw new RuntimeException(sprintf('Could not write to "%s".', $this->path));
        }

        $this->cache = $data;
    }

    public function clear()
    {
        if (file_exists($this->path)) {
            if (!@unlink($this->path)) {
                throw new RuntimeException(sprintf('Could not delete "%s".', $this->path));
            }
        }

        $this->cache = null;
    }
}
