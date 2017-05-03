<?php
namespace app\admin\model;

use think\Model;
use think\Hook;

class Crontab extends Base{
	//类型转换
    protected $type = [
    	'time'      =>  'timestamp',
        'execution'  =>  'timestamp'
    ];

    public function run($action="",$id){
        Hook::exec('app\\admin\\behavior\\Cache','CacheCrontab');
        $this->where('id',$id)->setField('execution',time());
    	switch ($action) {
    		case 'mysqlBackup':
    			$this->mysqlBackup();
    			break;
    	}
    	return false;
    }

    private function mysqlBackup(){
		$config=config('database');
		$db = new \util\Manage ( $config['hostname'].':'.($config['hostport']?$config['hostport']:'3306'), $config['username'], $config['password'], $config['database'], 'utf8' );
		$db->backup ("",APP_PATH.'..'.DS.'backup'.DS,10000); 
		return true;   	
    }

}