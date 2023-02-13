<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use App\Account;
use App\TemplateMessage;
use App\TemplateMessageAttachment;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Services\MessageService;

use App\Role;

class TemplateController extends Controller
{
    protected $messageService;

    public function __construct(MessageService $messageService)
    {
        $this->messageService = $messageService;
    }

    public function index()
    {
        $user = Auth::user();
        $isAdmin = $user->hasRole(Role::ROLE_ACCOUNT_ADMINISTRATOR, $user->account->id);
        $canEdit = $isAdmin ? $isAdmin : $user
                    ->hasRole(Role::ROLE_SIMULTANEOUS_DISTRIBUTION_EDITING_IS_POSSIBLE, $user->account->id);

        return view('template')->with('canEdit', $canEdit);
    }

    public function copy(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = Auth::user();
            $template = TemplateMessage::findOrFail($request->input('id'));
            $data = $template->toArray();
            $data = array_except($data, ['id']);
            $data['title'] .= ' - copy';
            $copyTemplate = $user->account->templateMessages()->create($data);

            foreach ($template->templateMessageAttachments as $templateMessageAttachment) {
                $copyTemplate->templateMessageAttachments()->create([
                    'media_file_id' => $templateMessageAttachment->media_file_id
                ]);
            }
            DB::commit();
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json(null, Response::HTTP_NOT_FOUND);
        }
        return response(null, Response::HTTP_OK);
    }

    public function lists()
    {
        $templates = Auth::user()->account->templateMessages;

        foreach ($templates as $template) {
            $template->attachments = collect($template->templateMessageAttachments)->map->mediaFile;
        }

        return response()->json($templates, Response::HTTP_OK);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $newTemplate = [
            'title' => $request->title,
            'content_message' => $request->content_message
        ];

        $templateMessage = $user->account->templateMessages()->create($newTemplate);

        // URL Action
        $templateMessage->recreateUrlAction($request->url_actions);
        $messageUrls = $templateMessage->messageUrls()->orderBy('id')->get();
        $formattedMessage = $this->messageService->formatMessage($user->account_id, $request->content_message, $messageUrls);
        $templateMessage->formatted_message = $formattedMessage;
        $templateMessage->save();

        if ($attachments = $request->attachment) {
            foreach ($attachments as $attachment) {
                if ($attachment) {
                    $newTemplateAtt = [
                        'template_message_id' => $templateMessage->id,
                        'media_file_id' => $attachment['id']
                    ];
                    $templateMessage->templateMessageAttachments()->create($newTemplateAtt);
                    $templateMessageAttachment[] = $attachment;
                }
            }
        }

        return response()->json($templateMessage, Response::HTTP_OK);
    }

    public function destroy($id)
    {
        try {
            $ids = explode(',', $id);
            foreach ($ids as $id) {
                $template = TemplateMessage::findOrFail($id);
                $template->delete();
            }
        } catch (ModelNotFoundException $e) {
            return response()->json(null, Response::HTTP_NOT_FOUND);
        }
        return response(null, Response::HTTP_OK);
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $template = TemplateMessage::findOrFail($id);
            // URL Action
            $template->recreateUrlAction($request->url_actions);
            $messageUrls = $template->messageUrls()->orderBy('id')->get();
            $formattedMessage = $this->messageService->formatMessage(Auth::user()->account_id, $request->content_message, $messageUrls);

            $data = (array)$request->all();
            $data = array_except($data, ['attachment']);
            $template->fill($data)->save();
            
            $template->templateMessageAttachments()->delete();

            foreach ($request->attachment as $attachment) {
                $template->templateMessageAttachments()->create(['media_file_id' => $attachment['id']]);
            }

            DB::commit();
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json(null, Response::HTTP_NOT_FOUND);
        }
        return response(null, Response::HTTP_OK);
    }
}
