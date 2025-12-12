<?php

namespace Tests\Feature;
use App\Services\CoursesService;
use Illuminate\Http\JsonResponse;
use PHPUnit\Framework\TestCase;

class CourseListUnitTestTest extends TestCase
{
    public function setUp(): void{
        parent::setUp();
        $this->mockReturned = [
            "success" => true,
            "message" => "Active courses retrieved successfully",
            "data" => [
                [
                    "course_id" => 1,
                    "course_name" => "Mathematics",
                    "status" => "active"
                ],
                [
                    "course_id" => 2,
                    "course_name" => "Science",
                    "status" => "active"
                ]
            ]
        ];
        $this->mockCoursesService = $this->createMock(CoursesService::class);
    }
    public function testActiveCoursesList(): void{
        $dataExpected = new JsonResponse($this->mockReturned, 200);
        $this->mockCoursesService
            ->method('getActiveCourses')
            ->willReturn($dataExpected);
        $response = $this->mockCoursesService->getActiveCourses();
        $this->assertEquals($dataExpected->getData()->success, $response->getData()->success);
        $this->assertEquals($dataExpected->getData()->message, $response->getData()->message);
        $this->assertEquals($dataExpected->getData()->data, $response->getData()->data);
    }
}
