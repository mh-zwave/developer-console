<?php

/**
 * Simple Model class
 *
 * @author Martin Vach
 */
class Model {

    private $db;

    /**
     * Class constructor
     * 
     * @param string $db
     * @return void
     */
    public function __construct($db) {
        $this->db = $db;
    }

    /**
     * Create SQL WHERE/AND
     * 
     * @param array $param
     * @return string
     */
    private function where($param) {
        if (!$param) {
            return '';
        }
        $attr = '';
        $cnt = 0;
        foreach ($param as $key => $value) {
            $cnt++;
            $attr .= ($cnt > 1 ? ' AND ' : ' WHERE ') . $key . ' = \'' . $value . '\'';
        }
        return $attr;
    }

    /**
     * Create SQL attributes from an array
     * 
     * @param array $param
     * @return string
     */
    private function setAttributes($param) {
        if (!$param) {
            return '';
        }
        $attr = '';
        foreach ($param as $key => $value) {
            $attr .= $key . ' = \'' . $value . '\',';
        }
        return rtrim($attr, ',');
    }

    /**
     * Create an user
     * 
     * @param array $param
     * @return bool
     */
    public function userCreate($param = array()) {
        $q = "INSERT user SET " . $this->setAttributes($param);
        return $this->db->query($q);
    }

    /**
     * Update an user
     * 
     * @param array $param
     * @param array $where
     * @return bool
     */
    public function userUpdate($param, $where) {
        $q = "UPDATE user SET " . $this->setAttributes($param) . $this->where($where);
        return $this->db->query($q);
    }

    /**
     * Load list of users
     * 
     * @param array $param
     * @return array
     */
    public function usersAll($param = array()) {
        $data = array();
        $q = "SELECT *, CONCAT(`first_name`, ' ', `last_name`) AS name FROM user";
        $q .= $this->where($param);
        $q .= " ORDER BY id DESC";
        $result = $this->db->query($q);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_object()) {
                unset($row->pw, $row->sid);
                array_push($data, $row);
            }
        }
        return $data;
    }

    /**
     * Load a single user
     * 
     * @param array $param
     * @return array
     */
    public function userFind($param) {
        $data = null;
        $q = "SELECT * FROM user " . $this->where($param);
        $result = $this->db->query($q);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_object()) {
                $data = $row;
            }
            unset($data->pw, $data->sid, $data->confirmed);
        }
        return $data;
    }

    /**
     * Deletean user
     * 
     * @param int $param
     * @return array
     */
    public function userDelete($param) {
        if (!is_array($param) || count($param) < 1) {
            return false;
        }
        $q = "DELETE FROM user " . $this->where($param);
        return $this->db->query($q);
    }

    /**
     * Load list of modules
     * 
     * @param array $param
     * @return array
     */
    public function modulesAll($param = array()) {
        $data = array();
        $q = "SELECT m.*,u.mail,"
                . " ROUND(AVG(IFNULL(r.score, 0))) AS rating, "
                . " SUM(IFNULL(c.isnew, 0)) AS commentsnew, "
                . " COUNT(distinct r.id) AS ratingscnt, "
                . " COUNT(distinct c.id) AS commentscnt "
                . " FROM modules m "
                . " LEFT JOIN user u ON m.user_id = u.id "
                . " LEFT JOIN ratings r ON m.id = r.module_id "
                . " LEFT JOIN comments c ON m.id = c.module_id ";
        $q .= $this->where($param);
        $q .= " GROUP BY m.id ORDER BY m.id DESC ";
        $result = $this->db->query($q);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_object()) {
                array_push($data, $row);
            }
        }
        return $data;
    }

    /**
     * Load list ofAPI  modules
     * 
     * @param array $param
     * @param string $ds
     * @return array
     */
    public function apiModulesAll($param = array(), $ids) {
        $data = array();
        $q = "SELECT m.*,u.mail,"
                . " ROUND(AVG(IFNULL(r.score, 0))) AS rating, "
                . " COUNT(distinct r.id) AS ratingscnt, "
                . " COUNT(distinct c.id) AS commentscnt "
                . " FROM modules m "
                . " LEFT JOIN user u ON m.user_id = u.id "
                . " LEFT JOIN ratings r ON m.id = r.module_id "
                . " LEFT JOIN comments c ON m.id = c.module_id ";
        $q .= $this->where($param);
        if ($ids !== '') {
            $q .= " OR m.id IN (" . $ids . ")";
        }
        $q .= " GROUP BY m.id ORDER BY m.id DESC ";
        $result = $this->db->query($q);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_object()) {
                array_push($data, $row);
            }
        }
        return $data;
    }

    /**
     * Load list of modules
     * 
     * @return array
     */
    public function modulesAllNotVerified() {
        $data = array();
        $q = "SELECT * FROM modules WHERE verified = 0 OR verified = 2 ";
        $result = $this->db->query($q);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_object()) {
                array_push($data, $row);
            }
        }
        return $data;
    }

    /**
     * Load a single module
     * 
     * @param int $param
     * @return array
     */
    public function moduleFind($param) {
        $data = array();
        $q = "SELECT * FROM modules " . $this->where($param);
        $result = $this->db->query($q);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_object()) {
                $data = $row;
            }
        }
        return $data;
    }
    /**
     * Load a single module with joined data
     * 
     * @param int $param
     * @return array
     */
    public function moduleFindJoin($param) {
        $data = array();
         $q = "SELECT m.*,u.mail,"
                . " ROUND(AVG(IFNULL(r.score, 0))) AS rating, "
                . " ROUND(AVG(IFNULL(r.score, 0)),1) AS ratingsavg, "
                . " COUNT(distinct r.id) AS ratingscnt, "
                . " COUNT(distinct c.id) AS commentscnt "
                . " FROM modules m "
                . " LEFT JOIN user u ON m.user_id = u.id "
                . " LEFT JOIN ratings r ON m.id = r.module_id "
                . " LEFT JOIN comments c ON m.id = c.module_id ";
        $q .= $this->where($param);
        $q .= " GROUP BY m.id ORDER BY m.id DESC ";
        $result = $this->db->query($q);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_object()) {
                $data = $row;
            }
        }
        return $data;
    }

    /**
     * Create a module
     * 
     * @param array $param
     * @return bool
     */
    public function moduleCreate($param = array()) {
        $q = "INSERT modules SET " . $this->setAttributes($param);
        return $this->db->query($q);
    }

    /**
     * Update a module
     * 
     * @param array $param
     * @return bool
     */
    public function moduleUpdate($param, $where) {
        $q = "UPDATE modules SET " . $this->setAttributes($param) . $this->where($where);
        return $this->db->query($q);
    }

    /**
     * Delete a module
     * 
     * @param int $param
     * @return array
     */
    public function moduleDelete($param) {
        if (!is_array($param) || count($param) < 1) {
            return false;
        }
        $q = "DELETE FROM modules " . $this->where($param);
        return $this->db->query($q);
    }

    /**
     * Load a single module lang
     * 
     * @param int $param
     * @return array
     */
    public function moduleLangFind($param) {
        $data = array();
        $q = "SELECT * FROM lang " . $this->where($param);
        $result = $this->db->query($q);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_object()) {
                $data = $row;
            }
        }
        return $data;
    }
    
     /**
     * Load a single archive
     * 
     * @param int $param
     * @return array
     */
    public function archiveFind($param) {
        $data = array();
        $q = "SELECT * FROM archiv " . $this->where($param);
        $result = $this->db->query($q);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_object()) {
                $data = $row;
            }
        }
        return $data;
    }
    
    /**
     * Load list of archives
     * 
     * @param array $param
     * @return array
     */
    public function archiveAll($param = array()) {
        $data = array();
        $q = "SELECT * FROM archiv ";
        $q .= $this->where($param);
        $q .= " ORDER BY id DESC";
        $result = $this->db->query($q);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_object()) {
                array_push($data, $row);
            }
        }
        return $data;
    }
     /**
     * Delete an archive
     * 
     * @param int $param
     * @return array
     */
    public function archiveDelete($param) {
        if (!is_array($param) || count($param) < 1) {
            return false;
        }
        $q = "DELETE FROM archiv " . $this->where($param);
        return $this->db->query($q);
    }

    /**
     * Load list of tokens
     * 
     * @param array $param
     * @return array
     */
    public function tokensAll($param = array()) {
        $data = array();
        $q = "SELECT * FROM token ";
        $q .= $this->where($param);
        $q .= " ORDER BY id DESC";
        $result = $this->db->query($q);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_object()) {
                array_push($data, $row);
            }
        }
        return $data;
    }

    /**
     * Load list of API tokens
     * 
     * @param string $tokens
     * @return string
     */
    public function apiTokensModuleIds($tokens) {
        $data = array();
        $q = "SELECT DISTINCT module_id FROM token WHERE token IN (" . $tokens . ") ";
        $result = $this->db->query($q);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_object()) {
                array_push($data, $row->module_id);
            }
        }
        return implode(',', $data);
    }

    /**
     * Create a token
     * 
     * @param array $param
     * @return bool
     */
    public function tokenCreate($param = array()) {
        $q = "INSERT token SET " . $this->setAttributes($param);
        return $this->db->query($q);
    }

    /**
     * Delete a token
     * 
     * @param int $param
     * @return array
     */
    public function tokenDelete($param) {
        if (!is_array($param) || count($param) < 1) {
            return false;
        }
        $q = "DELETE FROM token " . $this->where($param);
        return $this->db->query($q);
    }

    /**
     * Load list of skins
     * 
     * @param array $param
     * @return array
     */
    public function skinsAll($param = array()) {
        $data = array();
        $q = "SELECT * FROM skins ";
        $q .= $this->where($param);
        $q .= " ORDER BY id DESC";
        $result = $this->db->query($q);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_object()) {
                array_push($data, $row);
            }
        }
        return $data;
    }

    /**
     * Load a single skin
     * 
     * @param int $param
     * @return array
     */
    public function skinFind($param) {
        $data = array();
        $q = "SELECT * FROM skins " . $this->where($param);
        $result = $this->db->query($q);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_object()) {
                $data = $row;
            }
        }
        return $data;
    }

    /**
     * Create a skin
     * 
     * @param array $param
     * @return bool
     */
    public function skinCreate($param = array()) {
        $q = "INSERT skins SET " . $this->setAttributes($param);
        return $this->db->query($q);
    }

    /**
     * Update a skin
     * 
     * @param array $param
     * @return bool
     */
    public function skinUpdate($param, $where) {
        $q = "UPDATE skins SET " . $this->setAttributes($param) . $this->where($where);
        return $this->db->query($q);
    }

    /**
     * Delete a skin
     * 
     * @param int $param
     * @return array
     */
    public function skinDelete($param) {
        if (!is_array($param) || count($param) < 1) {
            return false;
        }
        $q = "DELETE FROM skins " . $this->where($param);
        return $this->db->query($q);
    }
    
    /**
     * Load list of icons
     * 
     * @param array $param
     * @return array
     */
    public function iconsAll($param = array()) {
        $data = array();
        $q = "SELECT * FROM icons ";
        $q .= $this->where($param);
        $q .= " ORDER BY id DESC";
        $result = $this->db->query($q);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_object()) {
                array_push($data, $row);
            }
        }
        return $data;
    }
    
    /**
     * Load a single icon
     * 
     * @param int $param
     * @return array
     */
    public function iconFind($param) {
        $data = array();
        $q = "SELECT * FROM icons " . $this->where($param);
        $result = $this->db->query($q);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_object()) {
                $data = $row;
            }
        }
        return $data;
    }
    
    /**
     * Create an icon
     * 
     * @param array $param
     * @return bool
     */
    public function iconCreate($param = array()) {
        $q = "INSERT icons SET " . $this->setAttributes($param);
        return $this->db->query($q);
    }
    
    /**
     * Update an icon
     * 
     * @param array $param
     * @return bool
     */
    public function iconUpdate($param, $where) {
        $q = "UPDATE icons SET " . $this->setAttributes($param) . $this->where($where);
        return $this->db->query($q);
    }
    
    /**
     * Delete an icon
     * 
     * @param int $param
     * @return array
     */
    public function iconDelete($param) {
        if (!is_array($param) || count($param) < 1) {
            return false;
        }
        $q = "DELETE FROM icons " . $this->where($param);
        return $this->db->query($q);
    }

    /**
     * Create a password
     * 
     * @param array $param
     * @return bool
     */
    public function passwordCreate($param = array()) {
        $q = "INSERT passwords SET " . $this->setAttributes($param);
        return $this->db->query($q);
    }

    /**
     * Update a password
     * 
     * @param array $param
     * @param array $where
     * @return bool
     */
    public function passwordUpdate($param, $where) {
        $q = "UPDATE passwords SET " . $this->setAttributes($param) . $this->where($where);
        return $this->db->query($q);
    }

    /**
     * Load a single password
     * 
     * @param int $param
     * @return array
     */
    public function passwordFind($param) {
        $data = array();
        $q = "SELECT * FROM passwords " . $this->where($param);
        $result = $this->db->query($q);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_object()) {
                $data = $row;
            }
        }
        return $data;
    }

    /**
     * Load list of comments
     * 
     * @param array $param
     * @return array
     */
    public function commentsAll($param = array()) {
        $data = array();
        $q = "SELECT * FROM comments ";
        $q .= $this->where($param);
        $q .= " ORDER BY id DESC";
        $result = $this->db->query($q);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_object()) {
                array_push($data, $row);
            }
        }
        return $data;
    }

    /**
     * Load a single comment
     * 
     * @param int $param
     * @return array
     */
    public function commentFind($param) {
        $data = array();
        $q = "SELECT * FROM comments " . $this->where($param);
        $result = $this->db->query($q);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_object()) {
                $data = $row;
            }
        }
        return $data;
    }

    /**
     * Create a comment
     * 
     * @param array $param
     * @return bool
     */
    public function commentCreate($param = array()) {
        $q = "INSERT comments SET " . $this->setAttributes($param);
        return $this->db->query($q);
    }

    /**
     * Update a comment
     * 
     * @param array $param
     * @return bool
     */
    public function commentUpdate($param, $where) {
        $q = "UPDATE comments SET " . $this->setAttributes($param) . $this->where($where);
        return $this->db->query($q);
    }

    /**
     * Delete a comment
     * 
     * @param int $param
     * @return array
     */
    public function commentDelete($param) {
        if (!is_array($param) || count($param) < 1) {
            return false;
        }
        $q = "DELETE FROM comments " . $this->where($param);
        return $this->db->query($q);
    }

    /**
     * Load list of ratings
     * 
     * @param array $param
     * @return array
     */
    public function ratingsAll($param = array()) {
        $data = array();
        $q = "SELECT * FROM ratings ";
        $q .= $this->where($param);
        $q .= " ORDER BY id DESC";
        $result = $this->db->query($q);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_object()) {
                array_push($data, $row);
            }
        }
        return $data;
    }
    
    /**
     * Load a single rating
     * 
     * @param int $param
     * @return array
     */
    public function ratingFind($param) {
        $data = array();
        $q = "SELECT * FROM ratings " . $this->where($param);
        $result = $this->db->query($q);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_object()) {
                $data = $row;
            }
        }
        return $data;
    }

    /**
     * Create a rating
     * 
     * @param array $param
     * @return bool
     */
    public function ratingCreate($param = array()) {
        $q = "INSERT ratings SET " . $this->setAttributes($param);
        return $this->db->query($q);
    }
    
    /**
     * Delete a rating
     * 
     * @param int $param
     * @return array
     */
    public function ratingDelete($param) {
        if (!is_array($param) || count($param) < 1) {
            return false;
        }
        $q = "DELETE FROM ratings " . $this->where($param);
        return $this->db->query($q);
    }
    
    /**
     * Delete a lang
     * 
     * @param int $param
     * @return array
     */
    public function langDelete($param) {
        if (!is_array($param) || count($param) < 1) {
            return false;
        }
        $q = "DELETE FROM lang " . $this->where($param);
        return $this->db->query($q);
    }

}
