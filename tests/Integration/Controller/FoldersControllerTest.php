<?php

declare(strict_types=1);

namespace App\Tests\Integration\Controller;

use App\Enum\PersonEnum;
use App\Exception\InvalidDataException;
use App\Exception\ResourceNotFoundException;
use App\Facade\InternalApi\DocumentFacade;
use Kyc\InternalApiBundle\Service\FolderService as InternalApiFolderService;
use App\Services\PersonService;
use App\Tests\BaseApiTest;
use App\Tests\Enum\BaseEnum;
use App\Tests\Mocks\Data\DocumentsData;
use App\Tests\Mocks\Data\FolderData;
use App\Tests\Mocks\Data\PersonData;
use Prophecy\Prophecy\ObjectProphecy;

class FoldersControllerTest extends BaseApiTest
{
    public const GET_FOLDER = 'api/v1/folders/1';
    public const FOLDER_GET_DOCUMENTS = 'api/v1/folders/1/documents';
    public const FOLDER_ADD_PERSON = 'api/v1/folders/1/add-person';
    public const FOLDER_ASSIGN_DOCUMENT_TO_PERSON = 'api/v1/folders/1/persons/6196610f9d67/documents/6184c9672f420';
    public const FOLDER_ASSIGN_DOCUMENT_TO_PERSON_NO_DOCUMENT = 'api/v1/folders/1/persons/6196610f9d67/documents/';

    protected ObjectProphecy $internalApiFolderService;
    protected ObjectProphecy $documentFacade;
    protected ObjectProphecy $personService;

    public function setUp(): void
    {
        parent::setUp();
        $this->internalApiFolderService = $this->prophesize(InternalApiFolderService::class);
        $this->documentFacade = $this->prophesize(DocumentFacade::class);
        $this->personService = $this->prophesize(PersonService::class);
        static::getContainer()->set(InternalApiFolderService::class, $this->internalApiFolderService->reveal());
        static::getContainer()->set(DocumentFacade::class, $this->documentFacade->reveal());
        static::getContainer()->set(PersonService::class, $this->personService->reveal());
    }

    public function testGetFolderById()
    {
        $this->internalApiFolderService->getFolderById(1)
            ->shouldBeCalledOnce()
            ->willReturn(FolderData::createFolderByIdModelResponse());
        $this->internalApiFolderService
            ->getPersonsByFolderId(1, FolderData::GET_FOLDER_BY_ID_ORDER_FILTERS)
            ->shouldBeCalledOnce()
            ->willReturn(PersonData::getFolderPersonsModelResponseByIdTestData());
        $this->requestWithBody(BaseEnum::METHOD_GET, self::GET_FOLDER);

        $this->assertEquals(200, $this->getStatusCode());
        $this->assertEquals(FolderData::getFolderByIdExpectedData(), $this->getResponseContent());
    }

    public function testGetFolderByIdNotFound()
    {
        $this->internalApiFolderService->getFolderById(1)->willThrow(new ResourceNotFoundException());
        $this->internalApiFolderService
            ->getPersonsByFolderId(1, FolderData::GET_FOLDER_BY_ID_ORDER_FILTERS)
            ->shouldNotBeCalled();
        $this->requestWithBody(BaseEnum::METHOD_GET, self::GET_FOLDER);

        $this->assertEquals(404, $this->getStatusCode());
    }

    public function testGetFolderPersonsDocuments()
    {
        $this->documentFacade->getDocumentsByFolderId(1)->shouldBeCalledOnce()->willReturn(DocumentsData::getInternalApiDocumentsByFolderId());
        $this->requestWithBody(BaseEnum::METHOD_GET, self::FOLDER_GET_DOCUMENTS);

        $this->assertEquals(200, $this->getStatusCode());
        $this->assertEquals(DocumentsData::getTestFolderPersonsDocumentExpectedData(), $this->getResponseContent());
    }

    public function testGetDocumentsException()
    {
        $exception = new InvalidDataException('Failed.');
        $this->documentFacade->getDocumentsByFolderId(1)->shouldBeCalledOnce()->willThrow($exception);
        $this->requestWithBody(BaseEnum::METHOD_GET, self::FOLDER_GET_DOCUMENTS);

        $this->assertEquals(400, $this->getStatusCode());
    }

    public function testAddPersonOk()
    {
        $body = [
            'personTypeId' => 1,
            'agencyId' => 709
        ];

        $this->personService
            ->addPerson(1, $body)
            ->shouldBeCalledOnce()
            ->willReturn(PersonData::DEFAULT_PERSON_UID_TEST_DATA);
        $this->requestWithBody(BaseEnum::METHOD_POST, self::FOLDER_ADD_PERSON, $body);

        $this->assertEquals(200, $this->getStatusCode());
        $this->assertEquals([PersonEnum::PERSON_UID => PersonData::DEFAULT_PERSON_UID_TEST_DATA], $this->getResponseContent());
    }

    public function testAddPersonEmptyBodyShouldThrowException()
    {
        $this->personService
            ->addPerson(1, [])
            ->shouldBeCalledOnce()
            ->willThrow(new InvalidDataException());

        $this->requestWithBody(BaseEnum::METHOD_POST, self::FOLDER_ADD_PERSON, []);
        $this->assertEquals(400, $this->getStatusCode());
    }

    public function testAssignDocumentOk()
    {
        $requestData = ["folderId" => 1, "personUid" => "6196610f9d67", "documentUid" => "6184c9672f420"];
        $this->personService
            ->assignDocument($requestData)
            ->shouldBeCalledOnce();

        $this->requestWithBody(BaseEnum::METHOD_PUT, self::FOLDER_ASSIGN_DOCUMENT_TO_PERSON);

        $this->assertEquals(204, $this->getStatusCode());
        $this->assertEquals(null, $this->getResponseContent());
    }

    public function testAssignDocumentNotFoundException()
    {
        $this->requestWithBody(BaseEnum::METHOD_PUT, self::FOLDER_ASSIGN_DOCUMENT_TO_PERSON_NO_DOCUMENT);
        $this->assertEquals(404, $this->getStatusCode());
    }
}
