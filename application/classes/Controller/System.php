<?php defined('SYSPATH') or die('No direct script access.');

class Controller_System extends Controller_Common {

	public function before()
	{
		parent::before();

		$this->title[] = 'System';
	}

	public function action_index()
	{
        $this->redirect('/system/deploy');
	}

    /**
     * страница сборки
     */
    public function action_deploy()
    {
        $this->title[] = 'Deploy';

        $version = Common::getVersion(true);

        $this->tpl
            ->bind('version', $version)
        ;
    }

    /**
     * страница запросов к базе
     */
    public function action_db()
    {
        $this->title[] = 'DB';
    }

    /**
     * выполнение кастомного запроса к базе
     */
    public function action_query()
    {
        $query = trim($this->request->post('query'));

        if (strpos($query, 'select') !== 0) {
            $html = 'Выполнять можно только SELECT';
        } else {
            $data = Oracle::init()->query($query);

            if (empty($data)) {
                $html = 'Запрос выдал пустой рузельтат';
            } else {
                $headers = array_keys($data[0]);

                $html = '<table class="table table_small">';
                $html .= '<tr>';

                foreach ($headers as $header) {
                    $html .= '<th>'. $header .'</th>';
                }

                $html .= '</tr>';

                foreach ($data as $row) {
                    $html .= '<tr>';
                    foreach ($row as $col) {
                        $html .= '<td>' . $col . '</td>';
                    }
                    $html .= '</tr>';
                }

                $html .= '</table>';
            }
        }

        $this->html($html);
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
    public function action_git()
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
