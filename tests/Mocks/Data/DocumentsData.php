<?php

declare(strict_types=1);

namespace App\Tests\Mocks\Data;

use DateTime;
use Kyc\InternalApiBundle\Model\Response\Document\DocumentByFolderModelResponse;
use Kyc\InternalApiBundle\Model\Response\Document\DocumentModelResponse;

class DocumentsData
{
    public const DEFAULT_DOCUMENT_UID_TEST_DATA = '617f896a61e39';

    public static function getInternalApiDocumentsByFolderId(): array
    {
        $dataSet = [
            [36090, '60a550c9e64a4', 0, "Pièce d'identité", 1, 2, 0, 3, 18534, 50000000000003, new DateTime('2021-05-19 17:55:36')],
            [36091, '60a550c9e64a4', 36090, "Pièce d'identité", 1, 2, 3, 3, 18534, 50000000000003, new DateTime('2021-05-19 17:56:00')],
            [36092, '60a55118ae17b', 0, "Avis d'imposition [A-1] sur le revenu [A-2]", 3, 1, 0, 0, 18534, 50000000000003, new DateTime('2021-05-19 17:55:36')],
            [36093, '60a55118ae17a', 0, "Avis d'imposition [A-1] sur le revenu [A-2]", 3, 4, 0, 0, 18534, 50000000000003, new DateTime('2021-05-19 17:55:36')],
        ];

        $documentsArray = [];
        foreach ($dataSet as $data) {
            array_push($documentsArray, (new DocumentByFolderModelResponse())
                ->setDocumentId($data[0])
                ->setDocumentUid($data[1])
                ->setMasterDocumentId($data[2])
                ->setName($data[3])
                ->setDocumentTypeId($data[4])
                ->setStatus($data[5])
                ->setStatusVerification($data[6])
                ->setStatusVerification2($data[7])
                ->setPersonId($data[8])
                ->setPersonVerification($data[9])
                ->setCreation($data[10]));
        }

        return $documentsArray;
    }

    public static function getInternalApiDocumentsResponse(bool $withContent = false): DocumentModelResponse
    {
        $documentData = new DocumentModelResponse();
        $documentData
            ->setDocumentId(36090)
            ->setDocumentUid("617f896a61e39")
            ->setMasterDocumentId(0)
            ->setName("Kbis (CompanyID)")
            ->setStatus(1)
            ->setData("a:2:{s:20:\"agence_document_type\";s:2:\"11\";s:16:\"controle_couleur\";i:0;}")
            ->setCreation(new DateTime('2021-11-01T06:30:02+00:00'))
            ->setDocumentTypeId(51)
            ->setSize(181333)
            ->setStatusVerification(0)
            ->setStatusVerification2(0)
            ->setAnomaly(null)
            ->setUrl("_TEMP_COMPANYID_1129_617f896a61e39.jpg")
            ->setSignature("6bba0ea97392769fffb14df19f7c850ba4c0bfdf9d214b490e001d7bbdfe335f")
            ->setEncryption(true)
            ->setCustomerAnomaly(null)
            ->setPartnerVerificationStatus(null)
            ->setSignatureInfos(null)
            ->setType("Kbis")
            ->setOrderDocument(21)
            ->setMandatory(null)
            ->setPersonDocumentId(null)
            ->setPartnerDocumentId("passport.jpeg");

        if ($withContent) {
           $documentData->setContent("/9j/4AAQSkZJRgABAQEAYABg...");
        }

        return $documentData;
    }

    public static function getTestDocumentByUidExpectedData(bool $withContent = false): array
    {
        $expected = [
            'name' => 'Kbis (CompanyID)',
            'documentId' => 36090,
            'documentUid' => '617f896a61e39',
            'masterDocumentId' => 0,
            'statusVerification' => 0,
            'statusVerification2' => 0,
            'status' => 1,
            'creation' => '2021-11-01T06:30:02+00:00',
            'personDocumentId' => null,
            'documentTypeId' => 51,
            'encryption' => true,
            'customerAnomaly' => null,
            'partnerVerificationStatus' => null,
            'data' => 'a:2:{s:20:"agence_document_type";s:2:"11";s:16:"controle_couleur";i:0;}',
            'size' => 181333,
            'anomaly' => null,
            'partnerDocumentId' => 'passport.jpeg',
            'url' => '_TEMP_COMPANYID_1129_617f896a61e39.jpg',
            'signature' => '6bba0ea97392769fffb14df19f7c850ba4c0bfdf9d214b490e001d7bbdfe335f',
            'signatureInfos' => null,
            'orderDocument' => 21,
            'type' => 'Kbis',
            'mandatory' => null,
        ];

        if ($withContent) {
            $expected['content'] = "/9j/4AAQSkZJRgABAQEAYABg...";
        }

        return $expected;
    }
}
