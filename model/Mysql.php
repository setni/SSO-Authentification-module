<?php

namespace SSO_PHP7\model;

/**
* SSO authentification
* @author Thomas D, Jean-Marc Dumond
* MIT License
*/
class Mysql {

    /**
    * @var instance of Mysql
    */
    private $mysqli;

    public function __construct ()
    {
        $this->mysqli = mysqli_init();
        try {
            if (!$this->$mysqli) {
                throw new \Exception("mysqli_init failed", 503);
            }
            if (!$this->$mysqli->real_connect(SQLIP, SQLUSER, SQLPWD, DATABASE, SQLPORT)) {
                throw new \Exception("connection error ".$this->$mysqli->error, 503);
            }
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    /**
    * @param $data Decrypt SSO data
    * @param $company company of SSO user
    * @author Thomas D,
    *
    */
    public function setUser (array $userData, string $company)
    : bool
    {
        $sql = "INSERT INTO core_users (
                user_FIRSTNAME,
                user_NAME,
                user_LOGIN,
                user_CRYPTPASSWORD,
                user_MAIL,
                user_PHONE,
                user_RIGHTS,
                user_NODES,
                user_CREATIONDATE,
                user_LANG,
                group_ID,
                company,
                service,
                user_VALIDITY,
                user_AUTHPOLICY) VALUES(?,?,?, '0000',?,?,
                    (SELECT group_RIGHTS FROM core_groups WHERE BINARY(group_LABEL) = ?),
                    1, NOW(),'FR',
                    (SELECT group_ID FROM core_groups WHERE BINARY(group_LABEL) = ?),
                    ?,?, '', ''
                )";

        if ($stmt = $this->mysqli->prepare($sql)) {
            $stmt->bind_param(
                "sssssss",
                $userData['firstname'],
                $userData['lastname'],
                $userData['firstname'],
                $userData['email'],
                $userData['phone'],
                $company,$company,$company,$company
            );

            if($stmt->execute()) {
                $this->_registeredSession($this->mysqli->insert_id);
                $stmt->close();
                return true;
            }
            $stmt->close();
            return false;
        }
        $stmt->close();
        return false;
    }

    /**
    * @param $login login of the user
    * @author Thomas D,
    *
    */
    public function connectUser (string $login)
    : bool
    {
        $sql = "SELECT user_ID FROM core_users WHERE user_MAIL = ?";
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param("s", $login);
        $stmt->execute();
        $stmt->bind_result($userID);
        $stmt->fetch();
        if ($userID > 0) {
            $this->_registeredSession($userID);
            $stmt->close();
            return true;
        }
        $stmt->close();
        return false;

    }

    public function __destruct()
    : void
    {
        $this->mysqli->close();
    }

    /**
    * @author Jean-Marc Dumond
    * from session manager
    */
    private function _registeredSession(int $user_id)
    : void
    {

        $query = "SELECT * FROM users_connected
                  WHERE user_id = $user_id AND auth_mode = ?";
        $stmt = $this->mysqli->prepare($query);
        $stmt->bind_param("s", "WEB");
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $subQuery = "DELETE FROM users_connected
                      WHERE user_id = $user_id AND auth_mode = 'WEB'";
            $subStmt = $this->mysqli->prepare($subQuery);
            $subStmt->bind_param("s", "WEB");
            $subStmt->execute();
            $subStmt->close();
        }
        $stmt->close();

        $query = "INSERT INTO users_connected (user_id,  session_id, connect_start, last_activity, auth_mode)
                  values(?,?,?,?,?)";

        $stmt = $this->mysqli->prepare($query);
        $stmt->bind_param("sssss",$user_id, session_id(), time(), time(), "WEB");
        $stmt->execute();
        $stmt->close();

        $query = "INSERT INTO FPlogs (user_id, connection_DATE)
        values(?,NOW())";
        $stmt = $this->mysqli->prepare($query);
        $stmt->bind_param("s",$user_id);
        $stmt->execute();
        $stmt->close();
    }

}
