<?php
/**
 *
 * @package    ${NAME}
 * @author     Shaun.Xu
 * @since      2018/1/12 14:41
 */
define("ROOT",dirname(dirname(__FILE__)));
define("PROJECT","App");
define("PROJECT_DIR",ROOT."/App");
require_once ROOT . "/zenphp/zenphp.php";
zenphp::run();