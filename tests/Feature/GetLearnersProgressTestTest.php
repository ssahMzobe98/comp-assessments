<?php
namespace Tests\Feature;

use App\Builders\LearnersMetaDataBuilder;
use App\Providers\Interfaces\ILearnersService;
use App\Repositories\LearnersRepo;
use App\Services\LearnersService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use PHPUnit\Framework\TestCase;

class GetLearnersProgressTestTest extends TestCase
{
    private $learnersMetaDataBuilderMock;
    private $learnersServiceMock;
    private $mockRequest;
    private $mockReturned;
    public function setUp(): void{
        parent::setUp();
        $this->learnersMetaDataBuilderMock = $this->createMock(LearnersMetaDataBuilder::class);
        $this->learnersServiceMock = $this->createMock(LearnersService::class);
        $this->learnersRepoMock = $this->createMock(LearnersRepo::class);
        $this->mockReturned = [
            "success" => true,
            "message" => "Success",
            "data" => [
                [
                    "id" => 1,
                    "full_name" => "Amahle Ndlovu",
                    "courses" => [
                        [
                            "course_id" => 4,
                            "course_name" => "Computer Applications Technology",
                            "progress" => 45
                        ],
                    ]
                ],
                [
                    "id" => 2,
                    "full_name" => "Angel Mkhize",
                    "courses" => [

                        [
                            "course_id" => 27,
                            "course_name" => "Tshivenda",
                            "progress" => 74
                        ],
                        [
                            "course_id" => 28,
                            "course_name" => "Visual Arts",
                            "progress" => 3
                        ]
                    ]
                ]
            ]
        ];
        $this->mockRequest = new Request([
            'page' => 1,
            'search' => '',
            'filterby' => '',
            'sort_by' => '',
            'sort_dir' => 'asc'
        ]);
    }
    public function testGetLearnersProgressSuccess():void{
        $dataExpected = new JsonResponse($this->mockReturned, 200);
        $this->learnersServiceMock
            ->expects($this->once())
            ->method('getLearnersProgress')
            ->with($this->mockRequest)
            ->willReturn($dataExpected);
        $data = $this->learnersServiceMock->getLearnersProgress($this->mockRequest);
        $this->assertEquals($dataExpected->getData()->success, $data->getData()->success);
        $this->assertEquals($dataExpected->getData()->message, $data->getData()->message);
        $this->assertEquals($dataExpected->getData()->data, $data->getData()->data);
    }
    public function testGetLearnersProgressFail():void{

        $dataExpected = new JsonResponse(
            [
                'success'=>false,
                'message'=>'Failed to get Learners due to internal erro. please contact support.'
            ], 400
        );

        $this->learnersServiceMock
            ->expects($this->once())
            ->method('getLearnersProgress')
            ->with($this->mockRequest)
            ->willReturn($dataExpected);
        $data = $this->learnersServiceMock->getLearnersProgress($this->mockRequest);
        $this->assertEquals($dataExpected->getData()->success, $data->getData()->success);
        $this->assertEquals($dataExpected->getData()->message, $data->getData()->message);
        $this->assertEquals($dataExpected->getData()->data, $data->getData()->data);
    }
    public function testMetaDataBuilder():void{
        $page = $this->mockRequest->get('page', 1);
        $page = max($page, 1);
        $limits = 5;
        $offset = ($page - 1) * $limits;
        $search = $this->mockRequest->get('search', '');
        $filterby = $this->mockRequest->get('filterby', '');
        $sortby = $this->mockRequest->get('sort_by', '');
        $sortDir = $this->mockRequest->get('sort_dir', 'asc');
        $filters = [
            'search' => $search,
            'filterby' => $filterby,
            'sort_by' => $sortby,
            'sort_dir' => $sortDir
        ];
        $expectedBuildResult = [
            "data" => $this->mockReturned['data'],
            "meta" => [
                "current_page" => $page,
                "last_page" => 10
            ]
        ];
        $this->learnersMetaDataBuilderMock->expects($this->once())
            ->method('setLearnersRepo')
            ->with($this->learnersRepoMock)
            ->willReturnSelf();

        $this->learnersMetaDataBuilderMock->expects($this->once())
            ->method('setLimit')
            ->with($limits)
            ->willReturnSelf();

        $this->learnersMetaDataBuilderMock->expects($this->once())
            ->method('setOffset')
            ->with($offset)
            ->willReturnSelf();

        $this->learnersMetaDataBuilderMock->expects($this->once())
            ->method('setFilters')
            ->with($filters)
            ->willReturnSelf();

        $this->learnersMetaDataBuilderMock->expects($this->once())
            ->method('setCurrentPage')
            ->with($page)
            ->willReturnSelf();

        $this->learnersMetaDataBuilderMock->expects($this->once())
            ->method('getLastPage')
            ->willReturnSelf();

        $this->learnersMetaDataBuilderMock->expects($this->once())
            ->method('setLearnersData')
            ->willReturnSelf();

        $this->learnersMetaDataBuilderMock->expects($this->once())
            ->method('build')
            ->willReturn($expectedBuildResult);
        $result = $this->learnersMetaDataBuilderMock->setLearnersRepo($this->learnersRepoMock)
            ->setLimit($limits)
            ->setOffset($offset)
            ->setFilters($filters)
            ->setCurrentPage($page)
            ->getLastPage()
            ->setLearnersData()
            ->build();
        $this->assertEquals($expectedBuildResult['data'], $result['data']);
        $this->assertEquals($expectedBuildResult['meta'], $result['meta']);
    }


}
