<?php defined('SYSPATH') or die('No direct script access.');

class Controller_System extends Controller_Common {

	public function before()
	{
		parent::before();

		$this->title[] = 'System';
	}

	public function action_index()
	{
	    $version = Common::getVersion(true);

        $this->tpl
            ->bind('version', $version)
        ;
	}

    /**
     * обновляем версию
     */
	public function action_versionRefresh()
    {
        $version = System::versionRefresh();

        $this->html($version);
    }

    /**
     * сборка frontend
     */
    public function action_gulp()
    {
        $command = $this->request->param('id') ?: 'build';

        $output = System::gulp($command);

        $this->html(var_export($output, 1));
    }

    /**
     * сборка backend
     */
    public function action_deploy()
    {
        $output = System::git();

        $this->html(var_export($output, 1));
    }

    /**
     * полная сборка
     */
    public function action_full()
    {
        $output = [
            'version'       => System::versionRefresh(),
            'gulp build'    => System::gulp('build'),
            'git'           => System::git(),
        ];

        $this->html(var_export($output, 1));
    }
}
