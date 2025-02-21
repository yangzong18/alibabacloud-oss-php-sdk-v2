<?php

namespace UnitTests\Transform;

use AlibabaCloud\Oss\V2\Models;
use AlibabaCloud\Oss\V2\Transform\BucketMetaQuery;
use AlibabaCloud\Oss\V2\OperationOutput;
use AlibabaCloud\Oss\V2\Utils;

class BucketMetaQueryTest extends \PHPUnit\Framework\TestCase
{
    public function testFromOpenMetaQuery()
    {
        // miss required field
        try {
            $request = new Models\OpenMetaQueryRequest();
            $input = BucketMetaQuery::fromOpenMetaQuery($request);
            $this->assertTrue(false, "should not here");
        } catch (\InvalidArgumentException $e) {
            $this->assertStringContainsString("missing required field, bucket", (string)$e);
        }

        $request = new Models\OpenMetaQueryRequest('bucket-123');
        $input = BucketMetaQuery::fromOpenMetaQuery($request);
        $this->assertEquals('bucket-123', $input->getBucket());
        $this->assertEquals('add', $input->getParameters()['comp']);
        $this->assertEquals('', $input->getParameters()['metaQuery']);
    }

    public function testToOpenMetaQuery()
    {
        // empty output
        $output = new OperationOutput();
        $result = BucketMetaQuery::toOpenMetaQuery($output);
        $this->assertEquals('', $result->status);
        $this->assertEquals(0, $result->statusCode);
        $this->assertEquals('', $result->requestId);
        $this->assertEquals(0, count($result->headers));

        $output = new OperationOutput(
            'OK',
            200,
            ['x-oss-request-id' => '123']
        );
        $result = BucketMetaQuery::toOpenMetaQuery($output);
        $this->assertEquals('OK', $result->status);
        $this->assertEquals(200, $result->statusCode);
        $this->assertEquals('123', $result->requestId);
        $this->assertEquals(1, count($result->headers));
        $this->assertEquals('123', $result->headers['x-oss-request-id']);
    }

    public function testFromGetMetaQueryStatus()
    {
        // miss required field
        try {
            $request = new Models\GetMetaQueryStatusRequest();
            $input = BucketMetaQuery::fromGetMetaQueryStatus($request);
            $this->assertTrue(false, "should not here");
        } catch (\InvalidArgumentException $e) {
            $this->assertStringContainsString("missing required field, bucket", (string)$e);
        }

        $request = new Models\GetMetaQueryStatusRequest('bucket-123');
        $input = BucketMetaQuery::fromGetMetaQueryStatus($request);
        $this->assertEquals('bucket-123', $input->getBucket());
        $this->assertEquals('', $input->getParameters()['metaQuery']);
    }

    public function testToGetMetaQueryStatus()
    {
        // empty output
        $output = new OperationOutput();
        $result = BucketMetaQuery::toGetMetaQueryStatus($output);
        $this->assertEquals('', $result->status);
        $this->assertEquals(0, $result->statusCode);
        $this->assertEquals('', $result->requestId);
        $this->assertEquals(0, count($result->headers));

        $body = '<?xml version="1.0" encoding="UTF-8"?>
<MetaQueryStatus>
  <State>Running</State>
  <Phase>FullScanning</Phase>
  <CreateTime>2021-08-02T10:49:17.289372919+08:00</CreateTime>
  <UpdateTime>2021-08-02T10:49:17.289372919+08:00</UpdateTime>
</MetaQueryStatus>';
        $output = new OperationOutput(
            'OK',
            200,
            ['x-oss-request-id' => '123', 'content-type' => 'application/xml'],
            body: Utils::streamFor($body)
        );
        $result = BucketMetaQuery::toGetMetaQueryStatus($output);
        $this->assertEquals('OK', $result->status);
        $this->assertEquals(200, $result->statusCode);
        $this->assertEquals('123', $result->requestId);
        $this->assertEquals(2, count($result->headers));
        $this->assertEquals('123', $result->headers['x-oss-request-id']);
        $this->assertEquals('application/xml', $result->headers['content-type']);
        $this->assertEquals('Running', $result->metaQueryStatus->state);
        $this->assertEquals('FullScanning', $result->metaQueryStatus->phase);
        $this->assertEquals('2021-08-02T10:49:17.289372919+08:00', $result->metaQueryStatus->createTime);
        $this->assertEquals('2021-08-02T10:49:17.289372919+08:00', $result->metaQueryStatus->updateTime);
    }

    public function testFromDoMetaQuery()
    {
        // miss required field 
        try {
            $request = new Models\DoMetaQueryRequest();
            $input = BucketMetaQuery::fromDoMetaQuery($request);
            $this->assertTrue(false, "should not here");
        } catch (\InvalidArgumentException $e) {
            $this->assertStringContainsString("missing required field, bucket", (string)$e);
        }

        try {
            $request = new Models\DoMetaQueryRequest('bucket-123');
            $input = BucketMetaQuery::fromDoMetaQuery($request);
            $this->assertTrue(false, "should not here");
        } catch (\InvalidArgumentException $e) {
            $this->assertStringContainsString("missing required field, metaQuery", (string)$e);
        }

        $request = new Models\DoMetaQueryRequest('bucket-123', new Models\MetaQuery(
            maxResults: 5,
            query: "{'Field': 'Size','Value': '1048576','Operation': 'gt'}",
            sort: 'Size',
            order: Models\MetaQueryOrderType::ASC,
            aggregations: new Models\MetaQueryAggregations(
            [
                new Models\MetaQueryAggregation(
                    field: 'Size',
                    operation: 'sum'
                ),
                new Models\MetaQueryAggregation(
                    field: 'Size',
                    operation: 'max'
                ),
            ]
        ),
            nextToken: 'MTIzNDU2Nzg6aW1tdGVzdDpleGFtcGxlYnVja2V0OmRhdGFzZXQwMDE6b3NzOi8vZXhhbXBsZWJ1Y2tldC9zYW1wbGVvYmplY3QxLmpw****'
        ));
        $input = BucketMetaQuery::fromDoMetaQuery($request);
        $this->assertEquals('bucket-123', $input->getBucket());
        $xml = <<<BBB
<?xml version="1.0" encoding="UTF-8"?><MetaQuery><MaxResults>5</MaxResults><Query>{'Field': 'Size','Value': '1048576','Operation': 'gt'}</Query><Sort>Size</Sort><Order>asc</Order><Aggregations><Aggregation><Field>Size</Field><Operation>sum</Operation></Aggregation><Aggregation><Field>Size</Field><Operation>max</Operation></Aggregation></Aggregations><NextToken>MTIzNDU2Nzg6aW1tdGVzdDpleGFtcGxlYnVja2V0OmRhdGFzZXQwMDE6b3NzOi8vZXhhbXBsZWJ1Y2tldC9zYW1wbGVvYmplY3QxLmpw****</NextToken></MetaQuery>
BBB;
        $this->assertEquals($xml, $this->cleanXml($input->getBody()->getContents()));
    }

    public function testToDoMetaQuery()
    {
        // empty output
        $output = new OperationOutput();
        $result = BucketMetaQuery::toDoMetaQuery($output);
        $this->assertEquals('', $result->status);
        $this->assertEquals(0, $result->statusCode);
        $this->assertEquals('', $result->requestId);
        $this->assertEquals(0, count($result->headers));

        $body = '<?xml version="1.0" encoding="UTF-8"?>
<MetaQuery>
  <NextToken>MTIzNDU2Nzg6aW1tdGVzdDpleGFtcGxlYnVja2V0OmRhdGFzZXQwMDE6b3NzOi8vZXhhbXBsZWJ1Y2tldC9zYW1wbGVvYmplY3QxLmpw****</NextToken>
  <Files>
    <File>
      <Filename>exampleobject.txt</Filename>
      <Size>120</Size>
      <FileModifiedTime>2021-06-29T15:04:05.000000000Z07:00</FileModifiedTime>
      <OSSObjectType>Normal</OSSObjectType>
      <OSSStorageClass>Standard</OSSStorageClass>
      <ObjectACL>default</ObjectACL>
      <ETag>"fba9dede5f27731c9771645a3986****"</ETag>
      <OSSCRC64>4858A48BD1466884</OSSCRC64>
      <OSSTaggingCount>2</OSSTaggingCount>
      <OSSTagging>
        <Tagging>
          <Key>owner</Key>
          <Value>John</Value>
        </Tagging>
        <Tagging>
          <Key>type</Key>
          <Value>document</Value>
        </Tagging>
      </OSSTagging>
      <OSSUserMeta>
        <UserMeta>
          <Key>x-oss-meta-location</Key>
          <Value>hangzhou</Value>
        </UserMeta>
      </OSSUserMeta>
    </File>
  </Files>
  <Aggregations>
    <Aggregation>
      <Field>Size</Field>
      <Operation>sum</Operation>
      <Value>4859250309</Value>
    </Aggregation>
    <Aggregation>
      <Field>Size</Field>
      <Operation>max</Operation>
      <Value>2235483240</Value>
    </Aggregation>
  </Aggregations>
</MetaQuery>';
        $output = new OperationOutput(
            'OK',
            200,
            ['x-oss-request-id' => '123', 'content-type' => 'application/xml'],
            body: Utils::streamFor($body)
        );
        $result = BucketMetaQuery::toDoMetaQuery($output);
        $this->assertEquals('OK', $result->status);
        $this->assertEquals(200, $result->statusCode);
        $this->assertEquals('123', $result->requestId);
        $this->assertEquals(2, count($result->headers));
        $this->assertEquals('application/xml', $result->headers['content-type']);
        $this->assertEquals('MTIzNDU2Nzg6aW1tdGVzdDpleGFtcGxlYnVja2V0OmRhdGFzZXQwMDE6b3NzOi8vZXhhbXBsZWJ1Y2tldC9zYW1wbGVvYmplY3QxLmpw****', $result->nextToken);

        $this->assertEquals(1, count($result->files->file));
        $this->assertEquals('exampleobject.txt', $result->files->file[0]->filename);
        $this->assertEquals(120, $result->files->file[0]->size);
        $this->assertEquals('2021-06-29T15:04:05.000000000Z07:00', $result->files->file[0]->fileModifiedTime);
        $this->assertEquals('Normal', $result->files->file[0]->ossObjectType);
        $this->assertEquals('Standard', $result->files->file[0]->ossStorageClass);
        $this->assertEquals('default', $result->files->file[0]->objectACL);
        $this->assertEquals('"fba9dede5f27731c9771645a3986****"', $result->files->file[0]->etag);
        $this->assertEquals(2, $result->files->file[0]->ossTaggingCount);
        $this->assertEquals('owner', $result->files->file[0]->ossTagging->taggings[0]->key);
        $this->assertEquals('John', $result->files->file[0]->ossTagging->taggings[0]->value);
        $this->assertEquals('type', $result->files->file[0]->ossTagging->taggings[1]->key);
        $this->assertEquals('document', $result->files->file[0]->ossTagging->taggings[1]->value);
        $this->assertEquals(1, count($result->files->file[0]->ossUserMeta->userMetas));
        $this->assertEquals('x-oss-meta-location', $result->files->file[0]->ossUserMeta->userMetas[0]->key);
        $this->assertEquals('hangzhou', $result->files->file[0]->ossUserMeta->userMetas[0]->value);
        $this->assertEquals('Size', ($result->aggregations->aggregations[0]->field));
        $this->assertEquals('sum', ($result->aggregations->aggregations[0]->operation));
        $this->assertEquals(4859250309, ($result->aggregations->aggregations[0]->value));
        $this->assertEquals('Size', ($result->aggregations->aggregations[1]->field));
        $this->assertEquals('max', ($result->aggregations->aggregations[1]->operation));
        $this->assertEquals(2235483240, ($result->aggregations->aggregations[1]->value));
    }

    public function testFromCloseMetaQuery()
    {
        // miss required field
        try {
            $request = new Models\CloseMetaQueryRequest();
            $input = BucketMetaQuery::fromCloseMetaQuery($request);
            $this->assertTrue(false, "should not here");
        } catch (\InvalidArgumentException $e) {
            $this->assertStringContainsString("missing required field, bucket", (string)$e);
        }

        $request = new Models\CloseMetaQueryRequest('bucket-123');
        $input = BucketMetaQuery::fromCloseMetaQuery($request);
        $this->assertEquals('bucket-123', $input->getBucket());
        $this->assertEquals('1B2M2Y8AsgTpgAmY7PhCfg==', $input->getHeaders()['content-md5']);
    }

    public function testToGetBucketMetaQuery()
    {
        // empty output
        $output = new OperationOutput();
        $result = BucketMetaQuery::toCloseMetaQuery($output);
        $this->assertEquals('', $result->status);
        $this->assertEquals(0, $result->statusCode);
        $this->assertEquals('', $result->requestId);
        $this->assertEquals(0, count($result->headers));

        $output = new OperationOutput(
            'OK',
            200,
            ['x-oss-request-id' => '123'],
        );
        $result = BucketMetaQuery::toCloseMetaQuery($output);
        $this->assertEquals('OK', $result->status);
        $this->assertEquals(200, $result->statusCode);
        $this->assertEquals('123', $result->requestId);
        $this->assertEquals(1, count($result->headers));
        $this->assertEquals('123', $result->headers['x-oss-request-id']);
    }

    private function cleanXml($xml): array|string
    {
        return str_replace("\n", "", str_replace("\r", "", $xml));
    }
}
