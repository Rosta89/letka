<?php
class User
{
    public $userId;
    public $username;
    private $admin;

    public function __construct($id)
    {
        $this->userId = $id;
        if ($id == -1) {
            $this->username = 'anonym';
            $this->admin = 0;
            return;
        }
        $result = Db::queryOne("SELECT * FROM USERS WHERE ID = ?", $id);
        $this->username = $result['username'];
        $this->admin = $result['admin'];
    }

    public function isAdmin()
    {
        if ($this->admin == 1) {
            return true;
        }
        return false;
    }

    public function isTeamAdmin($teamId)
    {
        $result = Db::querySingle("SELECT PLAYER_ROLE FROM players_2_teams pt 
                                    JOIN players pl ON pl.ID = pt.player_id
                                    WHERE pt.TEAM_ID = ? AND pl.USER_ID = ?", $teamId, $this->userId);
        if ($this->admin == 1 || $result == 1) {
            return true;
        }
        return false;
    }
}
