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

class Model implements ModelInterface
{
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

        $db = Db::get();
        $db->query($query);

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
        Db::query('INSERT into ' . Common::prefixTable("shortcode") . '
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
        return Db::fetchRow('Select * from ' . Common::prefixTable('shortcode') . '
     where code = ? ORDER BY id DESC LIMIT 1', array($shortcode));
    }

    /**
     * @param $url
     *
     * @return string
     */
    public function selectShortcodeByUrl($url)
    {
        return Db::fetchOne('Select code from ' . Common::prefixTable('shortcode') . '
     where url = ? ORDER BY id DESC LIMIT 1', array($url));
    }

    /**
     * @return array
     */
    public function selectShortcodesTrackedLocally()
    {
        return Db::fetchAll('Select id from ' . Common::prefixTable('shortcode') . '
     where url = 1');
    }
}