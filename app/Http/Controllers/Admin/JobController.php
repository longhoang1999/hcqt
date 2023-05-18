<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests;
use App\Http\Requests\CreateJobRequest;
use App\Http\Requests\UpdateJobRequest;
use App\Repositories\JobRepository;
use App\Http\Controllers\AppBaseController as InfyOmBaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use App\Models\Job;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class JobController extends InfyOmBaseController
{
    /** @var  JobRepository */
    private $jobRepository;

    public function __construct(JobRepository $jobRepo)
    {
        $this->jobRepository = $jobRepo;
    }

    /**
     * Display a listing of the Job.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {

        $this->jobRepository->pushCriteria(new RequestCriteria($request));
        $jobs = $this->jobRepository->all();
        return view('admin.jobs.index')
            ->with('jobs', $jobs);
    }

    /**
     * Show the form for creating a new Job.
     *
     * @return Response
     */
    public function create()
    {
        return view('admin.jobs.create');
    }

    /**
     * Store a newly created Job in storage.
     *
     * @param CreateJobRequest $request
     *
     * @return Response
     */
    public function store(CreateJobRequest $request)
    {
        $input = $request->all();

        $job = $this->jobRepository->create($input);

        Flash::success('Job saved successfully.');

        return redirect(route('admin.jobs.index'));
    }

    /**
     * Display the specified Job.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $job = $this->jobRepository->findWithoutFail($id);

        if (empty($job)) {
            Flash::error('Job not found');

            return redirect(route('jobs.index'));
        }

        return view('admin.jobs.show')->with('job', $job);
    }

    /**
     * Show the form for editing the specified Job.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $job = $this->jobRepository->findWithoutFail($id);

        if (empty($job)) {
            Flash::error('Job not found');

            return redirect(route('jobs.index'));
        }

        return view('admin.jobs.edit')->with('job', $job);
    }

    /**
     * Update the specified Job in storage.
     *
     * @param  int              $id
     * @param UpdateJobRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateJobRequest $request)
    {
        $job = $this->jobRepository->findWithoutFail($id);

        

        if (empty($job)) {
            Flash::error('Job not found');

            return redirect(route('jobs.index'));
        }

        $job = $this->jobRepository->update($request->all(), $id);

        Flash::success('Job updated successfully.');

        return redirect(route('admin.jobs.index'));
    }

    /**
     * Remove the specified Job from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
      public function getModalDelete($id = null)
      {
          $error = '';
          $model = '';
          $confirm_route =  route('admin.jobs.delete',['id'=>$id]);
          return View('admin.layouts/modal_confirmation', compact('error','model', 'confirm_route'));

      }

       public function getDelete($id = null)
       {
           $sample = Job::destroy($id);

           // Redirect to the group management page
           return redirect(route('admin.jobs.index'))->with('success', Lang::get('message.success.delete'));

       }

}
