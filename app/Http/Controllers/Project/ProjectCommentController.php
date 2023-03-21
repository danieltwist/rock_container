<?php

namespace App\Http\Controllers\Project;

use App\Events\NotificationReceived;
use App\Events\TelegramNotify;
use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Project;
use App\Models\ProjectComment;
use Illuminate\Http\Request;

class ProjectCommentController extends Controller
{
    public function addComment(Request $request)
    {

        $new_comment = new ProjectComment();
        $user = auth()->user();

        $project = Project::find($request->project_id);

        if($request->answer_to != ''){
            $new_comment->answer_to = $request->answer_to;
        }
        $new_comment->user_id = $user->id;
        $new_comment->project_id = $request->project_id;
        $new_comment->comment = $request->comment;

        if($request->hasFile('file')) {

            if($project->active == '1'){

                $path = 'public/Проекты/Активные проекты/'.$project["name"].'/Комментарии';

            }
            else {

                $path = 'public/Проекты/Завершенные проекты/'.$project["name"].'/Комментарии';

            }

            $new_comment->file = $request->file->storeAs($path, renameBeforeUpload($request->file->getClientOriginalName()));

        }

        $new_comment->save();

        if($request->notify_users != ''){
            $text = $user->name.' ' . __('project.send_message_in_project') . ' '.$project->name.':'.PHP_EOL.'<b>'.$request->comment.'</b>';
            $link = 'project/'.$project->id;

            foreach ($request->notify_users as $user_id){

                $message = [
                    'from' => 'Система',
                    'to' => $user_id,
                    'text' => $text,
                    'link' => $link,
                    'class' => 'bg-info'
                ];

                $message['inline_keyboard'] = [
                    'inline_keyboard' => [
                        [
                            ['text' => 'Открыть', 'url' => config('app.url').$message['link']],
                            ['text' => 'Ответить', 'callback_data' => 'answer:project:'.$new_comment->id]
                        ],
                    ]
                ];

                $message['action'] = 'answer:project:'.$new_comment->id.':start_message';

                $notification_channel = getNotificationChannel($message['to']);

                if($notification_channel == 'Система'){
                    event(new NotificationReceived($message));
                }
                elseif($notification_channel == 'Telegram'){
                    event(new TelegramNotify($message));
                }
                else {
                    event(new NotificationReceived($message));
                    event(new TelegramNotify($message));
                }
            }

        }

        return response()->json([
            'bg-class' => 'bg-success',
            'from' => 'Система',
            'message' => __('project.comment_was_successfully_added'),
            'ajax' => view('project.ajax.project_additional_info', [
                'comments' => $project->comments
            ])->render(),
            'button' => view('project.ajax.project_comments_button', [
                'comments' => $project->comments,
                'project' => $project
            ])->render(),
            'comments' => $project->comments,
            'project' => $project
        ]);

    }

    public function removeComment(Request $request){

        $comment = ProjectComment::findOrFail($request->comment_id);
        $project = Project::find($comment->project_id);

        $comment->delete();

        return response()->json([
            'bg-class' => 'bg-success',
            'from' => 'Система',
            'message' => __('project.comment_was_successfully_removed'),
            'ajax' => view('project.ajax.project_additional_info', [
                'comments' => $project->comments
            ])->render(),
            'button' => view('project.ajax.project_comments_button', [
                'comments' => $project->comments,
                'project' => $project
            ])->render(),
            'comments' => $project->comments,
            'project' => $project
        ]);

    }
}
