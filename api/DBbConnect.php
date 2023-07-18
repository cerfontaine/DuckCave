<?php
	/**
	* Database Connection
	*/
	class DbConnect {
		private $server = 'dblink';
		private $dbname = 'dbname';
		private $user = 'dbuser';
		private $pass = 'dbpassword';

		public function connect() {
			try {
				$conn = new PDO('mysql:host=' .$this->server .';dbname=' . $this->dbname, $this->user, $this->pass);
				$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				return $conn;
			} catch (\Exception $e) {
				echo "Database Error: " . $e->getMessage();
			}
		}
        
	}
?>