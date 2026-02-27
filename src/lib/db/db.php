<?php
    namespace lib\db;
    use PDO;
    class db {
        private $config;
        public function __construct()
        {
            $this->config = require(__DIR__ . '/../../config.php');
        }

        public function getWebDbCon(): PDO
        {
            $conf = $this->config['web'];

            $ret = new PDO(
                $conf['dsn'],
                $conf['user'],
                $conf['password'],
                [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]
            );

            return $ret;
        }

        public function getPanalDbCon(): PDO
        {
            $conf = $this->config['panal'];

            $ret = new PDO(
                $conf['dsn'],
                $conf['user'],
                $conf['password'],
                [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]
            );

            return $ret;
        }
    }
?>