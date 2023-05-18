<?php

namespace App\Http\Controllers;

use App\Common\Constant\ProjectConstants;
use App\Helpers\RoomHelper;
use App\Models\Room;
use App\Models\Project;
use App\Models\Project_User;
use App\Models\User;
use App\Models\Task;
use App\Models\Task_Request;
use App\Models\Task_User;
use App\Models\Task_History;
use App\Services\ProjectService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ProjectController extends Base\BaseController
{
    public function __construct()
    {
    }
    public function addOrUpdateProject(Request $request)
    {
        error_log('------dangkyCongViec------' . $request->project_id);
        $classify = isset($request->classify) ? $request->classify : 99;
        if (isset($request->project_id)) {
            $project = Project::find($request->project_id);
        } else {
            $project = new Project();
            $project->status = 1;
            // $project->status = ProjectConstants::STATUS_PROJECT_DANG_THUC_HIEN;
        }
        $project->name = $request->name;
        $project->description = $request->description;
        $project->classify = $classify;
        $project->exp_date = $request->exp_date;
        $project->is_important = $request->is_important;
        $project->is_urgent = $request->is_urgent;
        $project->assign_id = auth()->user()->id;
        error_log('------dangkyCongViec1------' . $request->main_id);
        DB::transaction(function () use ($project, $request) {
            $project->save();
            //Add project_user
            $lstProject_User = array();
            $lstProject_User[] = [
                'project_id' => $project->id,
                'user_id' => $request->main_id,
                'is_main' => 1
            ];
            if (isset($request->member_ids) && count($request->member_ids) > 0) {
                foreach ($request->member_ids as $member_id) {
                    error_log('------dangkyCongViec2------' . $member_id);
                    if ($member_id == $request->main_id) {
                        continue;
                    }
                    $projectUser = Project_User::where('project_id', $project->id)->where('user_id', $member_id)->first();
                    if (isset($projectUser)) {
                        continue;
                    }
                    $lstProject_User[] = [
                        'project_id' => $project->id,
                        'user_id' => $member_id,
                        'is_main' => 0
                    ];
                }
            }
            Project_User::insert($lstProject_User);
            return $this->responseJson([
                'status' => 'ok',
                'project' => $project,
            ]);
        }, 3);
    }
    public function addOrUpdateTask(Request $request)
    {
        error_log('------dangkyzxc------' . $request->project_id.'---------'.$request->id);
        $project = Project::find($request->project_id);
        if (!isset($project)) {
            return $this->responseJson([
                'status' => 'err',
                'message' => 'Không tồn tại công việc',
            ]);
        }
        if (isset($request->id)) {
            $task = Task::find($request->id);
        }else {
            $task = new Task();
            $task->status = 1;
            // $task->status = ProjectConstants::STATUS_TASK_DANG_THUC_HIEN;
        }
        $task->name = $request->name;
        $task->description = $request->description;
        $task->exp_date = $request->exp_date;
        $task->project_id  = $project->id;
        DB::transaction(function () use ($task, $request,$project) {
            $task->save();
            //Add project_user
            $lstTask_User = array();
            if (isset($request->task_user) && count($request->task_user) > 0) {
                foreach ($request->task_user as $user_id) {
                Task_User::updateOrCreate([
                    'task_id' => $task->id,
                    'user_id' => $user_id], [
                    'created_at' => Carbon::now(),
                ]);
                }
            }
            return $this->responseJson([
                'status' => 'ok',
                'task' => $task,
            ]);
        }, 3);
    }
    public function addOrUpdateTaskRequest(Request $request) {
        try {
            error_log('------dangky2222zxc------' . $request->level.'---------'.$request->classify);
            $project = Project::find($request->project_id);
            if (!isset($project)) {
                return $this->responseJson([
                    'status' => 'err',
                    'message' => 'Không tồn tại công việc',
                ]);
            }
            if (isset ($request->task_id)) {
                $task = Task::find($request->task_id);
                if (!isset($task)) {
                    return $this->responseJson([
                        'status' => 'err',
                        'message' => 'Không tồn tại công việc con',
                    ]);
                }
            }
            $main_user = ProjectService::getMainProject($project);
            if ($request->level ==1 && auth()->user()->id != $main_user->id) {
                return $this->responseJson([
                    'status' => 'err',
                    'message' => 'Bạn không có quyền tạo yêu cầu bổ sung cho dự án',
                ]);
            }
            if (isset($request->id)) {
                $task_request = Task_Request::find($request->id);
            }else {
                $task_request = new Task_Request();
            }
            error_log('-------------------'.is_array($request->user_add_ids));
            if (is_array($request->user_add_ids)) {
                $task_request->user_add_ids = implode(',',$request->user_add_ids);
            } else {
                $task_request->user_add_ids = null;
            }
            $task_request->task_id = $request->task_id;
            $task_request->project_id = $request->project_id;
            $task_request->Exp_date_request = $request->Exp_date_request;
            $task_request->classify  = $request->classify;
            $task_request->level  = $request->level;
            $task_request->user_id  = auth()->user()->id;
            error_log('------dangky3------' . $request->project_id.'---------'.$request->task_id);
            DB::transaction(function () use ($task_request) {
                $task_request->save();
            }, 3);
        }catch(Exception $e){
            error_log($e->getMessage());
            return $this->responseJson([
                'status' => 'err',
            ]);
        }
        return $this->responseJson([
            'status' => 'ok',
            'task_request' => $task_request,
        ]);
    }
    public function allProjectByClassify(Request $request)
    {
        $classify = $request->classify;
        if (isset(auth()->user()->id)) {
            $user = User::find(auth()->user()->id);
            $user->load('project');
            $lst_project = $user->project;
            $lst_project_assign = Project::where('assign_id', auth()->user()->id)->get();
            $lst_project = $lst_project->merge($lst_project_assign);
            $lst_project->unique();
            $lst_project = $lst_project->filter(function($project)use($classify) {
                error_log('---------------------'.$classify);
                return $project->classify == $classify;
            });
            foreach ($lst_project as $project) {
                $project->load('users');
            }
            return $this->responseJson([
                'status' => 'ok',
                'lst_project' => $lst_project,
            ]);
        }
    }


    public function getProjectById(Request $request)
    {
        error_log('getProjectById---------------' . $request->id);
        $project = Project::find($request->id)->load('users')->load('task')->load('attachedFiles');
        
        foreach ($project->task as $task) {
          $task->load('users')->load('attachedFiles');
        }
        return $this->responseJson([
            'status' => 'ok',
            'project' => $project,
        ]);
    }

    public function getTaskById(Request $request)
    {
        error_log('getTaskById---------------' . $request->task_id);
        if (isset($request->task_id)) {
            $task = Task::find($request->task_id)->load('users')->load('project')->load('attachedFiles');
        }
        return $this->responseJson([
            'status' => 'ok',
            'task' => $task,
        ]);
    }

    public function getAllTaskRequest(Request $request)
    {
        error_log('getTaskById---------------' . $request->project_id);
        $project = Project::find($request->project_id);
            if (!isset($project)) {
                return $this->responseJson([
                    'status' => 'err',
                    'message' => 'Không tồn tại công việc',
                ]);
            }
        $project->load('users');
        $main_user = ProjectService::getMainProject($project);
        $user = auth()->user();
        error_log($main_user->id);
        $list_task_request = [];
        if ($user->id == $project->assign_id || $user->id == $main_user->id) {
            $list_task_request = Task_Request::where('project_id', $project->id)
            ->get()->load('task')->load('project')->load('user');
        }else {
            $list_task_request = Task_Request::where('project_id', $project->id)
            ->where('user_id', $user->id)->get()->load('task')->load('project')->load('user');
        }
        foreach ($list_task_request as $task_request) {
            if (isset($task_request->task)) {
                $task_request->task->load('users');
                }
            $task_request->project->load('users');
        }
        return $this->responseJson([
            'status' => 'ok',
            'list_task_request' => $list_task_request,
        ]);
    }

    public function changeStatusTaskRequest(Request $request){
        error_log('---------------------------'.$request->id);
        $task_request = Task_Request::find($request->id)->load('project')->load('task');
        $task_request->project->load('users');
        if (!isset($task_request)) {
            return $this->responseJson([
                'status' => 'err',
                'message' => 'Không tồn tại yêu cầu bổ sung',
            ]);
        }
        if ($request->status == 1) {
            $task_request->status = $request->status;
            $task_request->reason = $request->reason;
            $task_request->save();
            return $this->responseJson([
                'status' => 'ok',
            ]);
        }
        // \App\Common\Constant\ProjectConstants::TASK_REQUEST_LEVEL_PROJECT
        if ($task_request->level == 1) {
            if (auth()->user()->id != $task_request->project->assign_id) {
                return $this->responseJson([
                    'status' => 'err',
                    'message' => 'Không có quyền phê duyệt yêu cầu bổ sung này',
                ]);
            }
            $project = Project::find($task_request->project_id);
            if ($task_request->classify == 1)
            {
                if ($request->status == 2) {
                    $project->exp_date = $request->Exp_date_request;
                }
                $task_request->status = $request->status;
                $task_request->Exp_date_request = $request->Exp_date_request;
                $task_request->save();
                $project->save();
                return $this->responseJson([
                    'status' => 'ok',
                ]);
            }
            if ($task_request->classify == 2) 
            {
                $lstProject_User = array();
                $user_add_ids = is_array($request->user_add_ids) ? $request->user_add_ids : explode(',', $request->user_add_ids);
                foreach ($user_add_ids as $user_add_id) {
                    error_log($user_add_id);
                    $project_user = Project_User::where('project_id',$project->id)->where('user_id',$user_add_id)->first();
                    if (isset($project_user)) {continue;}
                    $lstProject_User[] = [
                        'project_id' => $project->id,
                        'user_id' => $user_add_id,
                        'is_main' => 0
                    ];
                }
                Project_User::insert($lstProject_User);
                $task_request->user_add_ids = implode(',',$request->user_add_ids);
                $task_request->status = $request->status;
                $task_request->save();
                return $this->responseJson([
                    'status' => 'ok',
                ]);
            }
        }
        // \App\Common\Constant\ProjectConstants::TASK_REQUEST_LEVEL_TASK
        if ($task_request->level == 2) {
            $task = Task::find($task_request->task_id);
            $main_user = ProjectService::getMainProject($task_request->project);
            if (auth()->user()->id != $main_user->id) {
                return $this->responseJson([
                    'status' => 'err',
                    'message' => 'Không có quyền phê duyệt yêu cầu bổ sung này',
                ]);
            }
            if ($task_request->classify == 1)
            {
                if ($request->status == 2) {
                    if ($request->Exp_date_request > $task_request->project->exp_date)
                    {
                        return $this->responseJson([
                            'status' => 'err',
                            'message' => 'Thời gian bổ sung vượt quá hạn hoàn thành công việc',
                        ]);
                    }
                    if (!isset($task)) {
                        return $this->responseJson([
                            'status' => 'err',
                            'message' => 'Không tồn tại công việc con của yêu cầu bổ sung',
                        ]);
                    }
                    $task->exp_date = $request->Exp_date_request;
                }
                $task_request->status = $request->status;
                $task_request->Exp_date_request = $request->Exp_date_request;
                $task->save();
                $task_request->save();
                return $this->responseJson([
                    'status' => 'ok',
                ]);
            }
            if ($task_request->classify == 2)
            {
                if(1 == 2) {
                    return $this->responseJson([
                        'status' => 'err',
                        'message' => 'Nhân sự bổ sung không nằm trong thành phần xử lý công việc',
                    ]);
                }
                if (!isset($task)) {
                    return $this->responseJson([
                        'status' => 'err',
                        'message' => 'Không tồn tại công việc con của yêu cầu bổ sung',
                    ]);
                }
                $lstTask_User = array();
                $user_add_ids = is_array($request->user_add_ids) ? $request->user_add_ids : explode(',', $request->user_add_ids);
                foreach ($user_add_ids as $user_add_id) {
                    error_log($user_add_id);
                    $task_user = Task_User::where('task_id',$task->id)->where('user_id',$user_add_id)->first();
                    if (isset($task_user)) {continue;}
                    $lstTask_User[] = [
                        'task_id' => $task->id,
                        'user_id' => $user_add_id,
                        'is_main' => 0
                    ];
                }
                error_log('22222222222222222');
                Task_User::insert($lstTask_User);
                $task_request->user_add_ids = implode(',',$request->user_add_ids);
                $task_request->status = $request->status;
                $task_request->save();
                return $this->responseJson([
                    'status' => 'ok',
                ]);
            }
        }
    }
    public function changeStatusProject(Request $request) {
        $project_id = $request->project_id;
        $project = Project::find($project_id);
        if (!isset($project)) {
            return $this->responseJson(array(
                'status'=>'ng',
                'message' => 'Công việc không tồn tại hoặc bị xóa'
            ));
        }
        $project->status = $request->status;
        $project->notice = $request->notice;
        $project->notice=trim($project->notice);
        $project->save();
        return $this->responseJson(array(
            'status'=>'ok',
            'project' => $project
        ));
    }
    public function changeStatusTask(Request $request)
    {
        $project_id = $request->project_id;
        $project = Project::find($project_id);
        if (!isset($project)) {
            return $this->responseJson(array(
                'status'=>'ng',
                'message' => 'Công việc không tồn tại hoặc bị xóa'
            ));
        }
        $task_id = $request->task_id;
        $task = Task::find($task_id);
        if (!isset($task)) {
            return $this->responseJson(array(
                'status'=>'ng',
                'message' => 'Công việc con không tồn tại hoặc bị xóa'
            ));
        }
        $task->status = $request->status;
        $task->notice = $request->notice;
        $task->notice=trim($task->notice);
        $task->save();
        error_log($task->status);
        return $this->responseJson(array(
            'status'=>'ok',
            'task' => $task
        ));
    }
    public function deleteProject(Request $request)
    {   
        
        $project = Project::where('id',$request->project_id)->first();
        if (!isset($project)) {
            return $this->responseJson(array(
                'status'=>'ng',
                'message' => 'Công việc không tồn tại hoặc bị xóa'
            ));
        }
        $projectUser = Project_User::where('project_id',$project->id)->delete();
        $tasks = Task::where('project_id',$project->id)->get();
        foreach ($tasks as $task) {
            $task_user = Task_User::where('task_id',$task->id)->delete();
            $task_request = Task_Request::where('task_id',$task->id)->delete();
            $task_history = Task_History::where('task_id',$task->id)->delete();
        }
        $project->delete();
        return $this->responseJson(array(
            'status'=>'ok',
        ));
    }
    public function deleteTask(Request $request)
    {
        $task = Task::where('id',$request->task_id)->delete();
        return $this->responseJson(array(
            'status'=>'ok',
        ));
    }
    public function deleteTaskRequest(Request $request)
    {
        $task = Task_request::where('id',$request->task_request_id)->delete();
        return $this->responseJson(array(
            'status'=>'ok',
        ));
    }
}
