<?php

namespace OnlinePayments\Sdk;

use ErrorException;
use Exception;
use OnlinePayments\Sdk\Domain\APIError;
use OnlinePayments\Sdk\Domain\ErrorResponse;
use ReflectionException;
use ReflectionMethod;
use stdClass;

/**
 * @group logging
 */
class CommunicatorLoggingTest extends ClientTestCase
{
    /**
     * @throws Exception
     */
    public function testOnlyLogWhileLoggingIsEnabled()
    {
        $connection = new TestingConnection(
            $this->getMockConnectionResponse(200, array('Content-Type' => 'application/json'))
        );
        /** @var Connection $connection */
        $communicator = new Communicator(
            $connection,
            $this->getMockCommunicatorConfiguration()
        );
        $logger = $this->getMockBuilder('\OnlinePayments\Sdk\CommunicatorLogger')->getMock();
        $logger->expects($this->exactly(2))->method('log')->will(
            $this->returnCallback(function ($message) {
                $messageParts = explode("\n", $message);
                $this->assertGreaterThanOrEqual(2, count($messageParts));
                if (strpos($messageParts[0], 'Outgoing request') === 0) {
                    $this->assertStringContainsString('/bar', $messageParts[1]);
                }
            })
        );
        $logger->expects($this->never())->method('logException');
        /** @var CommunicatorLogger $logger */
        $responseClassMap = $this->getMockResponseClassMap();
        $communicator->get($responseClassMap, '/foo');
        $communicator->enableLogging($logger);
        $communicator->get($responseClassMap, '/bar');
        $communicator->disableLogging();
        $communicator->get($responseClassMap, '/baz');
    }

    /**
     * @throws Exception
     */
    public function testLoggingForSuccessResponse()
    {
        $relativeRequestUri = '/foo/bar';
        $connection = new TestingConnection(
            $this->getMockConnectionResponse(200, array('Content-Type' => 'application/json'))
        );
        /** @var Connection $connection */
        $communicator = new Communicator(
            $connection,
            $this->getMockCommunicatorConfiguration()
        );
        $relativeRequestUriWithRequestParameters = $relativeRequestUri;
        $requestHeaders =
            $this->getCommunicatorRequestHeaders($communicator, 'POST', $relativeRequestUriWithRequestParameters);
        $requestBody = $this->getMockRequestDataObject();
        $httpObfuscator = new HttpObfuscator();
        $rawObfuscatedRequest = $httpObfuscator->getRawObfuscatedRequest(
            'POST',
            $relativeRequestUriWithRequestParameters,
            $requestHeaders,
            $requestBody->toJson()
        );
        $logger = $this->getMockBuilder('\OnlinePayments\Sdk\CommunicatorLogger')->getMock();
        $logger->expects($this->exactly(2))->method('log')->will(
            $this->returnCallback(function ($message) use ($rawObfuscatedRequest) {
                $messageHeader = strstr($message, "\n", true);
                if (strpos($messageHeader, 'Outgoing request') === 0) {
                    $this->assertEquals(trim(strstr($message, "\n")), $rawObfuscatedRequest);
                }
            })
        );
        $logger->expects($this->never())->method('logException');
        /** @var CommunicatorLogger $logger */
        $communicator->enableLogging($logger);
        $responseClassMap = $this->getMockResponseClassMap();
        $communicator->post($responseClassMap, $relativeRequestUri, '', $requestBody);
    }

    /**
     * @throws Exception
     */
    public function testLoggingForSuccessUTF8Response()
    {
        $relativeRequestUri = '/foo/bar';
        $connection = new TestingConnection(
            $this->getMockConnectionResponse(200, array('Content-Type' => 'application/json;charset=UTF-8'))
        );
        /** @var Connection $connection */
        $communicator = new Communicator(
            $connection,
            $this->getMockCommunicatorConfiguration()
        );
        $relativeRequestUriWithRequestParameters = $relativeRequestUri;
        $requestHeaders =
            $this->getCommunicatorRequestHeaders($communicator, 'POST', $relativeRequestUriWithRequestParameters);
        $requestBody = $this->getMockRequestDataObject();
        $httpObfuscator = new HttpObfuscator();
        $rawObfuscatedRequest = $httpObfuscator->getRawObfuscatedRequest(
            'POST',
            $relativeRequestUriWithRequestParameters,
            $requestHeaders,
            $requestBody->toJson()
        );
        $logger = $this->getMockBuilder('\OnlinePayments\Sdk\CommunicatorLogger')->getMock();
        $logger->expects($this->exactly(2))->method('log')->will(
            $this->returnCallback(function ($message) use ($rawObfuscatedRequest) {
                $messageHeader = strstr($message, "\n", true);
                if (strpos($messageHeader, 'Outgoing request') === 0) {
                    $this->assertEquals(trim(strstr($message, "\n")), $rawObfuscatedRequest);
                }
            })
        );
        $logger->expects($this->never())->method('logException');
        /** @var CommunicatorLogger $logger */
        $communicator->enableLogging($logger);
        $responseClassMap = $this->getMockResponseClassMap();
        $communicator->post($responseClassMap, $relativeRequestUri, '', $requestBody);
    }

    /**
     * @throws Exception
     */
    public function testLoggingForClientErrorResponse()
    {
        $relativeRequestUri = '/foo/bar';
        $responseHeaders = array('Content-Type' => 'application/json');
        $errorResponse = $this->getErrorResponseDataObject();
        $connectionResponse = $this->getMockConnectionResponse(400, $responseHeaders, $errorResponse->toJson());
        $connection = new TestingConnection($connectionResponse);
        /** @var Connection $connection */
        $communicator = new Communicator(
            $connection,
            $this->getMockCommunicatorConfiguration()
        );
        $httpObfuscator = new HttpObfuscator();
        $rawObfuscatedResponse = $httpObfuscator->getRawObfuscatedResponse($connectionResponse);
        $logger = $this->getMockBuilder('\OnlinePayments\Sdk\CommunicatorLogger')->getMock();
        $logger->expects($this->exactly(2))->method('log')->will(
            $this->returnCallback(function ($message) use ($rawObfuscatedResponse) {
                $messageHeader = strstr($message, "\n", true);
                if (strpos($messageHeader, 'Incoming response') === 0) {
                    $this->assertEquals(trim(strstr($message, "\n")), $rawObfuscatedResponse);
                }
            })
        );
        $logger->expects($this->never())->method('logException');
        /** @var CommunicatorLogger $logger */
        $communicator->enableLogging($logger);
        $responseClassMap = $this->getMockResponseClassMap();
        /** @var ResponseClassMap $responseClassMap */
        try {
            $communicator->put($responseClassMap, $relativeRequestUri);
        } catch (ResponseException $e) {
            return;
        }
        $this->fail('an expected exception has not been raised');
    }

    /**
     * @throws Exception
     */
    public function testLoggingForInvalidResponse()
    {
        $relativeRequestUri = '/foo/bar';
        $responseHeaders = array('Content-Type' => 'text/html');
        $responseBody = 'an error occurred';
        $connectionResponse = $this->getMockConnectionResponse(400, $responseHeaders, $responseBody);
        $connection = new TestingConnection($connectionResponse);
        /** @var Connection $connection */
        $communicator = new Communicator(
            $connection,
            $this->getMockCommunicatorConfiguration()
        );
        $httpObfuscator = new HttpObfuscator();
        $rawObfuscatedResponse = $httpObfuscator->getRawObfuscatedResponse($connectionResponse);
        $logger = $this->getMockBuilder('\OnlinePayments\Sdk\CommunicatorLogger')->getMock();
        $logger->expects($this->exactly(2))->method('log')->will(
            $this->returnCallback(function ($message) use ($rawObfuscatedResponse) {
                $messageHeader = strstr($message, "\n", true);
                if (strpos($messageHeader, 'Incoming response') === 0) {
                    $this->assertEquals(trim(strstr($message, "\n")), $rawObfuscatedResponse);
                }
            })
        );
        $logger->expects($this->never())->method('logException');
        /** @var CommunicatorLogger $logger */
        $communicator->enableLogging($logger);
        $responseClassMap = $this->getMockResponseClassMap();
        /** @var ResponseClassMap $responseClassMap */
        try {
            $communicator->get($responseClassMap, $relativeRequestUri);
        } catch (InvalidResponseException $e) {
            $this->assertEquals($connectionResponse->getHttpStatusCode(), $e->getResponse()->getHttpStatusCode());
            $this->assertEquals($connectionResponse->getHeaders(), $e->getResponse()->getHeaders());
            $this->assertEquals($connectionResponse->getBody(), $e->getResponse()->getBody());
            return;
        }
        $this->fail('an expected exception has not been raised');
    }

    /**
     * @throws Exception
     */
    public function testLoggingForCommunicationException()
    {
        $relativeRequestUri = '/foo/bar';
        $errorException = new ErrorException('Test error exception');
        $connection = new TestingConnection(null, $errorException);
        /** @var Connection $connection */
        $communicator = new Communicator(
            $connection,
            $this->getMockCommunicatorConfiguration()
        );
        $logger = $this->getMockBuilder('\OnlinePayments\Sdk\CommunicatorLogger')->getMock();
        $logger->expects($this->once())->method('log')->will(
            $this->returnCallback(function ($message) {
                $messageHeader = strstr($message, "\n", true);
                $this->assertStringContainsString('Outgoing request', $messageHeader);
            })
        );
        $logger->expects($this->once())->method('logException')->will(
            $this->returnCallback(function ($message, $exception) use ($errorException) {
                $this->assertEquals($errorException, $exception);
            })
        );
        /** @var CommunicatorLogger $logger */
        $communicator->enableLogging($logger);
        $responseClassMap = $this->getMockResponseClassMap();
        /** @var ResponseClassMap $responseClassMap */
        try {
            $communicator->delete($responseClassMap, $relativeRequestUri);
        } catch (ErrorException $e) {
            return;
        }
        $this->fail('an expected exception has not been raised');
    }

    /**
     * @throws Exception
     */
    public function testLogWithRealRequest()
    {
        $logger = $this->getMockBuilder('\OnlinePayments\Sdk\CommunicatorLogger')->getMock();
        $logger->expects($this->exactly(2))->method('log')->will(
            $this->returnCallback(function ($message) {
                $messageParts = explode("\n", $message);
                $this->assertGreaterThanOrEqual(2, count($messageParts));
                if (strpos($messageParts[0], 'Outgoing request') === 0) {
                    $this->assertStringContainsString('/services/testconnection', $messageParts[1]);
                }
            })
        );
        $logger->expects($this->never())->method('logException');
        /** @var CommunicatorLogger $logger */
        $client = $this->getClient();
        $client->enableLogging($logger);
        $client->merchant($this->getMerchantId())->services()->testConnection();
        $client->disableLogging();
    }

    /**
     * @param int $httpStatusCode
     * @param string[] $headers
     * @param string $body
     * @return ConnectionResponse
     */
    protected function getMockConnectionResponse($httpStatusCode, $headers = array(), $body = '{}')
    {
        $connectionResponse = $this->getMockBuilder('\OnlinePayments\Sdk\ConnectionResponse')->getMock();
        $connectionResponse->method('getHttpStatusCode')->willReturn($httpStatusCode);
        $connectionResponse->method('getHeaders')->willReturn($headers);
        $returnMap = array();
        foreach ($headers as $key => $value) {
            $returnMap[] = array($key, $value);
        }
        $connectionResponse->method('getHeaderValue')->willReturnMap($returnMap);
        $connectionResponse->method('getBody')->willReturn($body);
        return $connectionResponse;
    }

    /**
     * @return CommunicatorConfiguration
     */
    protected function getMockCommunicatorConfiguration()
    {
        return $this->getMockBuilder('\OnlinePayments\Sdk\CommunicatorConfiguration')->disableOriginalConstructor()->getMock();
    }

    /**
     * @return ResponseClassMap
     */
    protected function getMockResponseClassMap()
    {
        return $this->getMockBuilder('\OnlinePayments\Sdk\ResponseClassMap')->disableOriginalConstructor()->getMock();
    }

    /**
     * @return DataObject
     */
    protected function getMockRequestDataObject()
    {
        $requestDataObject = $this->getMockBuilder('\OnlinePayments\Sdk\DataObject')->getMock();
        $convertedDataObject = new stdClass();
        $convertedDataObject->customer = new stdClass();
        $convertedDataObject->customer->firstName = 'John';
        $convertedDataObject->customer->lastname = 'Doe';
        $convertedDataObject->accountNumber = '1234567890';
        $requestDataObject->method('toObject')->willReturn($convertedDataObject);
        $requestDataObject->method('toJson')->willReturn(json_encode($convertedDataObject));
        return $requestDataObject;
    }

    /**
     * @return DataObject
     */
    protected function getErrorResponseDataObject()
    {
        $errorResponse = new ErrorResponse();
        $errorResponse->setErrorId('123;');
        $apiError = new APIError();
        $apiError->setCode('code');
        $apiError->setHttpStatusCode(400);
        $apiError->setMessage('Test');
        $apiError->setPropertyName('foo');
        $errorResponse->setErrors(array($apiError));
        return $errorResponse;
    }

    /**
     * @param Connection $connection
     * @return CommunicatorLoggerHelper
     * @throws ReflectionException
     */
    protected function getCommunicatorLoggerHelper(Connection $connection)
    {
        $method = new ReflectionMethod($connection, 'getCommunicatorLoggerHelper');
        $method->setAccessible(true);
        return $method->invoke($connection);
    }

    /**
     * @param Communicator $communicator
     * @param $httpMethod
     * @param $relativeUriPathWithRequestParameters
     * @param string $clientMetaInfo
     * @param CallContext|null $callContext
     * @return string[]
     * @throws ReflectionException
     */
    protected function getCommunicatorRequestHeaders(
        Communicator $communicator,
                     $httpMethod,
                     $relativeUriPathWithRequestParameters,
                     $clientMetaInfo = '',
        CallContext  $callContext = null
    )
    {
        $method = new ReflectionMethod($communicator, 'getRequestHeaders');
        $method->setAccessible(true);
        return $method->invoke(
            $communicator,
            $httpMethod,
            $relativeUriPathWithRequestParameters,
            Communicator::MIME_APPLICATION_JSON,
            $clientMetaInfo,
            $callContext
        );
    }
}
