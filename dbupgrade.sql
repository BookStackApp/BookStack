
   Illuminate\Database\QueryException 

  SQLSTATE[HY000] [1045] Access denied for user 'forge'@'localhost' (using password: NO) (Connection: mysql, SQL: SHOW TABLES)

  at vendor\laravel\framework\src\Illuminate\Database\Connection.php:825
    821▕                     $this->getName(), $query, $this->prepareBindings($bindings), $e
    822▕                 );
    823▕             }
    824▕ 
  ➜ 825▕             throw new QueryException(
    826▕                 $this->getName(), $query, $this->prepareBindings($bindings), $e
    827▕             );
    828▕         }
    829▕     }

  1   vendor\laravel\framework\src\Illuminate\Database\Connectors\Connector.php:66
      PDOException::("SQLSTATE[HY000] [1045] Access denied for user 'forge'@'localhost' (using password: NO)")

  2   vendor\laravel\framework\src\Illuminate\Database\Connectors\Connector.php:66
      PDO::__construct("mysql:host=localhost;port=3306;dbname=forge", "forge", Object(SensitiveParameterValue), [])

