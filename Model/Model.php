<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link    http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\ShortcodeTracker\Model;

use Piwik\Common;
use Piwik\Db;

/**
 * @codeCoverageIgnore
 */
class Model implements ModelInterface
{

    /**
     * @var Db
     */
    protected $db;

    /**
     * @return mixed
     */
    public function getDb()
    {
        if ($this->db === null) {
            $this->db = Db::get();
        }

        return $this->db;
    }

    /**
     * @param mixed $db
     */
    public function setDb($db)
    {
        $this->db = $db;
    }


    /**
     * @return bool
     */
    public function install()
    {
        $query = "CREATE TABLE IF NOT EXISTS " . Common::prefixTable('shortcode') . "
        ( `id` int(11) NOT NULL AUTO_INCREMENT,
         `code` varchar(6) NOT NULL,
         `url` varchar(512) NOT NULL,
         `idsite` int(10) DEFAULT NULL,
         PRIMARY KEY(`id`),
         UNIQUE KEY `uk_code` (`code`)
         ) DEFAULT CHARSET = utf8;";

        $this->executeQuery('query', $query);

        return true;
    }

    /**
     * @param string $code
     * @param string $url
     * @param int    $idsite
     *
     * @return bool
     */
    public function insertShortcode($code, $url, $idsite)
    {
        $this->getDb()->query('INSERT into ' . Common::prefixTable("shortcode") . '
      SET code = ?, url = ?, idsite = ?', array($code, $url, $idsite));
    }

    /**
     * @throws /Exception
     * @return bool
     */
    public function deleteShortcode()
    {
        return false;
    }

    /**
     * @param $shortcode
     *
     * @return string
     */
    public function selectShortcodeByCode($shortcode)
    {
        return $this->getDb()->fetchRow('Select * from ' . Common::prefixTable('shortcode') . '
     where code = ? ORDER BY id DESC LIMIT 1', array($shortcode));
    }

    /**
     * @param $url
     *
     * @return string
     */
    public function selectShortcodeByUrl($url)
    {
        return $this->getDb()->fetchOne('Select code from ' . Common::prefixTable('shortcode') . '
     where url = ? ORDER BY id DESC LIMIT 1', array($url));
    }

    /**
     * @param string $method
     * @param string $query
     * @param array  $params
     */
    public function executeQuery($method, $query, array $params = array())
    {
        $this->getDb()->$method($query, $params);
    }

}