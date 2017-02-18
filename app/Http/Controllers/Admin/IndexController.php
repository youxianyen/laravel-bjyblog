<?php

namespace App\Http\Controllers\Admin;

use App\Models\Config;
use App\Models\FriendshipLink;
use DB;
use App\Models\Article;
use App\Models\ArticleTag;
use App\Models\Comment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use League\HTMLToMarkdown\HtmlConverter;
use Markdownify\Converter;
use Markdownify\ConverterExtra;

class IndexController extends Controller
{
    /**
     * 后台首页
     *
     * @return mixed
     */
    public function index()
    {
        return view('admin/index/index');
    }


    public function migration(Article $articleModel, ArticleTag $articleTag, Comment $commentModel, FriendshipLink $friendshipLinkModel, Config $configModel)
    {
        // $htmlConverter = new HtmlConverter(['strip_tags' => true]);
        // $test = DB::connection('old')->table('article')->where('aid', 103)->first();
        // $content = $test->content;
        // $content = htmlspecialchars_decode($content);
        //
        // $str = new \HTML_To_Markdown($content);
        //
        // echo $str;die;
        //
        // preg_match_all("/>(.*)?<\/a>/", $content, $a);
        // p($a);die;
        // $content = str_replace('textvalue="https://github.com/baijunyao/thinkphp-bjyadmin"', '', $content);
        // echo $content;
        // $htmlConverter = new \HTML_To_Markdown();
        // $htmlConverter->set_option('strip_tags', true);
        //
        // $content = str_replace(['<pre class="brush:', '</pre>', ';toolbar:false">', '&nbsp;'], ["\r\n```", "\r\n```\r\n", "\r\n", ' '], $content);
        // $content = str_replace(["\r\n", ' '], ['|rn|', '|s|'], $content);
        // $content = $htmlConverter->convert($content);
        // $content = str_replace(['|rn|', '|s|'], ["\r\n", ' '], $content);
        // echo ($content);die;





        // 从旧系统中迁移文章
        // $htmlConverter = new HtmlConverter(['strip_tags' => true]);
        $htmlConverter = new \HTML_To_Markdown();
        $htmlConverter->set_option('strip_tags', true);
        $data = DB::connection('old')->table('article')->get()->toArray();
        $articleModel->truncate();
        foreach ($data as $k => $v) {
            $content = htmlspecialchars_decode($v->content);

            $content = str_replace('/Upload/image/ueditor', 'uploads/article', $content);

            $content = str_replace(['<pre class="brush:', '</pre>', ';toolbar:false">', '&nbsp;'], ["\r\n```", "\r\n```\r\n", "\r\n", ' '], $content);
            $content = str_replace('```js', '```javascript', $content);
            $content = str_replace(["\r\n"], ['|rn|'], $content);
            $markdown = $htmlConverter->convert($content);
            $markdown = str_replace(['|rn|', '\*'], ["\r\n", '*'], $markdown);

            $markdown = str_replace('http://www.baijunyao.com/uploads/article', 'uploads/article', $markdown);

            $article = [
                'id' => $v->aid,
                'category_id' => $v->cid,
                'title' => $v->title,
                'author' => $v->author,
                'content' => $markdown,
                'description' => $v->description,
                'keywords' => $v->keywords,
                'cover' => $articleModel->getCover($markdown),
                'is_top' => $v->is_top,
                'click' => $v->click,
            ];
            $articleModel->create($article);
            $editArticleMap = [
                'id' => $v->aid
            ];

            $editArticleData = [
                'created_at' => date('Y-m-d H:i:s', $v->addtime)
            ];
            $articleModel->editData($editArticleMap, $editArticleData);
        }

        // 从旧系统中迁移文章标签中间表
        // $data = DB::connection('old')->table('article_tag')->get()->toArray();
        // $articleTag->truncate();
        // foreach ($data as $v) {
        //     $article_tag = [
        //         'article_id' => $v->aid,
        //         'tag_id' => $v->tid
        //     ];
        //     $articleTag->addData($article_tag);
        // }

        // 从旧系统中迁移评论
        // $data = DB::connection('old')->table('comment')->get()->toArray();
        // $commentModel->truncate();
        // foreach ($data as $v) {
        //     $comment_data = [
        //         'id' => $v->cmtid,
        //         'oauth_user_id' => $v->ouid,
        //         'type' => $v->type,
        //         'pid' => $v->pid,
        //         'article_id' => $v->aid,
        //         'content' => $v->content,
        //         'status' => $v->status,
        //     ];
        //     $commentModel->create($comment_data);
        //     $editCommentMap = [
        //         'id' => $v->cmtid,
        //     ];
        //     $editCommentData = [
        //         'created_at' => date('Y-m-d H:i:s', $v->date)
        //     ];
        //     $commentModel->editData($editCommentMap, $editCommentData);
        // }
        
        // 迁移友情链接
        // $data = DB::connection('old')->table('link')->get()->toArray();
        // $friendshipLinkModel->truncate();
        // foreach ($data as $v) {
        //     $link_data = [
        //         'id' => $v->lid,
        //         'name' => $v->lname,
        //         'url' => $v->url,
        //         'sort' => $v->sort
        //     ];
        //     if ($v->is_show === 0) {
        //         $link_data['deleted_at'] = date('Y-m-d H:i:s', time());
        //     }
        //     $friendshipLinkModel->addData($link_data);
        // }

        // 迁移配置项
        // $data = DB::connection('old')->table('config')->get()->toArray();
        // $configModel->truncate();
        // foreach ($data as $v) {
        //     $config_data = [
        //         'id' => $v->id,
        //         'name' => $v->name,
        //         'value' => $v->value
        //     ];
        //     $configModel->addData($config_data);
        // }

    }

}
