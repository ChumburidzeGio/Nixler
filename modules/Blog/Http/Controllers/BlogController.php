<?php

namespace Modules\Blog\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use League\CommonMark\CommonMarkConverter;

class BlogController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        return view('blog::index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return view('blog::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
    }

    /**
     * Show the specified resource.
     * @return Response
     */
    public function show()
    {
        $converter = new CommonMarkConverter();

        $post = json_decode(json_encode([
            'title' => '18 самых ярких женщин современности с врожденным чувством стиля',
            'body' => ''
        ]));

        return $converter->convertToHtml('
                Чувство стиля невозможно купить за деньги. Женщины, которые им обладают, всегда заслуживают восхищения. 
                AdMe.ru составил свой список знаменитостей, чей стиль достоин подражания.

                ### Амаль Клуни

                ![Амаль Клуни] : https://files1.adme.ru/files/news/part_150/1503715/136565-18393760-col1-0-1493032795-1493032798-650-1-1493032798-650-6f4f44027b-1493107172.jpg
                
                Амаль не только супруга актера Джорджа Клуни, но и успешная правозащитница и авдокат. Ее работа наложила определенный отпечаток на стиль. Амаль всегда одета уместно и изысканно. 

                ### Кейт Бланшетт

                ![Кейт Бланшетт](https://files2.adme.ru/files/news/part_150/1503715/136515-18393960-col2-0-1493032837-1493032839-650-1-1493032839-650-8b58dd0cc4-1493107172.jpg)

                Актриса Кейт Бланшетт умудряется одеваться одновременно стильно и удобно. Ее никогда не увидишь в слишком обтягивающем платье или на слишком высоких каблуках. Она всегда заметно выделяется на красных дорожках. 
            ');

        return view('blog::show', compact('post'));
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit()
    {
        return view('blog::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update(Request $request)
    {
    }

    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    public function destroy()
    {
    }
}
