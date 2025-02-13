<?php

declare(strict_types=1);

/**
 * @copyright  2020 Ad Aures
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html AGPL3
 * @link       https://castopod.org/
 */

namespace Modules\Admin\Controllers;

use App\Entities\Page;
use App\Models\PageModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RedirectResponse;

class PageController extends BaseController
{
    public function _remap(string $method, string ...$params): mixed
    {
        if ($params === []) {
            return $this->{$method}();
        }

        if (($page = (new PageModel())->find($params[0])) instanceof Page) {
            return $this->{$method}($page);
        }

        throw PageNotFoundException::forPageNotFound();
    }

    public function list(): string
    {
        $this->setHtmlHead(lang('Page.all_pages'));
        $data = [
            'pages' => (new PageModel())->findAll(),
        ];

        return view('page/list', $data);
    }

    public function view(Page $page): string
    {
        $this->setHtmlHead($page->title);
        return view('page/view', [
            'page' => $page,
        ]);
    }

    public function createView(): string
    {
        helper('form');

        $this->setHtmlHead(lang('Page.create'));
        return view('page/create');
    }

    public function createAction(): RedirectResponse
    {
        $page = new Page([
            'title'            => $this->request->getPost('title'),
            'slug'             => $this->request->getPost('slug'),
            'content_markdown' => $this->request->getPost('content'),
        ]);

        $pageModel = new PageModel();

        if (! $pageModel->insert($page)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('errors', $pageModel->errors());
        }

        return redirect()
            ->route('page-list')
            ->with('message', lang('Page.messages.createSuccess', [
                'pageTitle' => $page->title,
            ]));
    }

    public function editView(Page $page): string
    {
        helper('form');

        $this->setHtmlHead(lang('Page.edit'));
        replace_breadcrumb_params([
            0 => $page->title,
        ]);
        return view('page/edit', [
            'page' => $page,
        ]);
    }

    public function editAction(Page $page): RedirectResponse
    {
        $page->title = $this->request->getPost('title');
        $page->slug = $this->request->getPost('slug');
        $page->content_markdown = $this->request->getPost('content');

        $pageModel = new PageModel();

        if (! $pageModel->update($page->id, $page)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('errors', $pageModel->errors());
        }

        return redirect()->route('page-edit', [$page->id])->with('message', lang('Page.messages.editSuccess'));
    }

    public function deleteAction(Page $page): RedirectResponse
    {
        (new PageModel())->delete($page->id);

        return redirect()->route('page-list');
    }
}
