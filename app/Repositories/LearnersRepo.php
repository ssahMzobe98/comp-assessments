<?php

namespace App\Repositories;

use App\Providers\Interfaces\ILearnersRepo;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class LearnersRepo implements ILearnersRepo
{
    /**
     * @param int $limit
     * @param int $offset
     * @param array $filters
     * @return Collection
     */
    public function getLearnersProgress(int $limit = 10, int $offset = 0, array $filters = []): Collection
    {
        $sortBy  = $filters['sort_by']  ?? null;
        $sortDir = $filters['sort_dir'] ?? 'asc';

        $query = DB::table('learners')->select(
            'learners.id',
            DB::raw("firstname || ' ' || lastname AS full_name")
        );
        if (!empty($filters['search'])) {
            $search = $filters['search'];

            $query->where(function ($q) use ($search) {
                $q->where('firstname', 'like', "%$search%")
                    ->orWhere('lastname', 'like', "%$search%")
                    ->orWhere('learners.id', $search);
            });
        }

        if (!empty($filters['filterby'])) {
            $filterCourse = (int) $filters['filterby'];

            $query->leftJoin('enrolments AS fe', 'learners.id', '=', 'fe.learner_id')
                ->where('fe.course_id', $filterCourse);
        }

        if ($sortBy === 'name') {
            $query->orderBy(DB::raw("firstname || ' ' || lastname"), $sortDir);
        }

        if ($sortBy === 'id') {
            $query->orderBy('learners.id', $sortDir);
        }

        if ($sortBy === 'average_progress') {

            $query->leftJoin('enrolments AS ea', 'learners.id', '=', 'ea.learner_id')
                ->selectRaw("
                    learners.*,
                    (
                        SELECT AVG(progress)
                        FROM enrolments
                        WHERE learner_id = learners.id
                    ) AS avg_progress
               ")
                ->orderBy('avg_progress', $sortDir);
        }

        if (str_starts_with($sortBy, 'course_progress_')) {

            $courseId = (int) str_replace('course_progress_', '', $sortBy);

            $query->leftJoin('enrolments AS e', function ($join) use ($courseId) {
                $join->on('learners.id', '=', 'e.learner_id')
                    ->where('e.course_id', '=', $courseId);
            });

            $query->addSelect(DB::raw('e.progress AS sort_progress'));

            if (!empty($filters['filterby']) && (int)$filters['filterby'] === $courseId) {
                $query->where('e.course_id', $courseId);
            }

            $query->orderBy('sort_progress', $sortDir);
        }

        $learners = $query->limit($limit)
            ->offset($offset)
            ->get();

        return $learners->map(function ($learner) use ($filters) {
            $learner->courses = $this->getLearnersCourses($learner->id, $filters)->toArray();
            return $learner;
        });
    }

    /**
     * @param int|null $learnerId
     * @param array $filters
     * @return Collection
     */
    public function getLearnersCourses(?int $learnerId, array $filters = []): Collection
    {
        $query = DB::table('enrolments')
            ->leftJoin('courses', 'courses.id', '=', 'enrolments.course_id')
            ->where('learner_id', $learnerId);

        if (!empty($filters['filterby'])) {
            $query->where('enrolments.course_id', '=', $filters['filterby']);
        }

        return $query->select(
            'courses.id as course_id',
            'courses.name as course_name',
            'enrolments.progress as progress'
        )->get();
    }
    /**
     * @param int $limit
     * @param array $filters
     * @return int
     */
    public function getLearnersProgressLastPage(int $limit = 10, array $filters = []): int
    {
        $query = DB::table('learners');
        $query = $this->search($filters, $query);
        $total = $query->count();
        return (int) ceil($total / $limit);
    }

    /**
     * @param array $filters
     * @param Builder $query
     * @return void
     */
    public function search(array $filters, Builder $query): Builder
    {
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query = $query->where(function ($q) use ($search) {
                $q->where('firstname', 'like', "%$search%")
                    ->orWhere('lastname', 'like', "%$search%")
                    ->orWhere('learners.id', $search);
            });
        }
        if (!empty($filters['filterby'])) {
            $query = $query->whereIn('learners.id', function ($sub) use ($filters) {
                $sub->from('enrolments')
                    ->select('learner_id')
                    ->where('course_id', $filters['filterby']);
            });
        }
        return $query;

    }

}