<?php

namespace chatapi\WhatsApp;

class ClientTest extends \PHPUnit_Framework_TestCase {

    private $phone;

    public function setUp() {
        $config = json_decode(file_get_contents('config.json'));
        Client::getInstance([
            'url' => $config->url,
            'token' => $config->token
        ]);

        $this->phone = $config->phone;
    }

    /**
     * Send message to phone
     */
    public function testSendMessageToPhone() {
        $message = 'Test Message to phone ' . rand(100000, 999999);
        $client = Client::getInstance();

        $result = $client->sendMessage([
            'phone' => $this->phone,
            'body' => $message
        ]);

        $this->assertInstanceOf('stdClass', $result);
        $this->assertAttributeEquals(
            true, 'sent', $result, 'Message successful sent'
        );
        return $message;
    }

    /**
     * Group creating
     */
    public function testCreateGroup() {
        $client = Client::getInstance();
        $message = 'Test message ' . rand(100000, 999999);
        $data = $client->createGroup('Test group', [$this->phone], $message);
        $this->assertObjectHasAttribute('created', $data);
        $this->assertAttributeEquals(true, 'created', $data);
        return $message;
    }

    /**
     * Receive messages
     * @return string
     */
    public function testGetMessages() {
        $client = Client::getInstance();

        $data = $client->getMessages();

        $this->assertObjectHasAttribute('messages', $data);
        $this->assertObjectHasAttribute('lastMessageNumber', $data);
        return true;
    }

    /**
     * Receive account status
     */
    public function testGetStatus() {
        $client = Client::getInstance();
        $data = $client->getStatus();
        $this->assertObjectHasAttribute('accountStatus', $data);
    }

    /**
     * Application logout
     */
    public function testLogout() {
        $this->markTestSkipped(
            'Logout will fail other tests'
        );
        $client = Client::getInstance();
        $data = $client->logout();
        $this->assertObjectHasAttribute('result', $data);
    }

    /**
     * Receive QR-code
     */
    public function testGetQrCode() {
        $client = Client::getInstance();
        $data = $client->getQrCode();
        $this->assertContains('PNG', $data);
    }

    /**
     * Webhook setting
     * @return string
     */
    public function testSetWebHook() {
        $url = 'http://testdomain.io/hook/' . rand(100000, 999999);
        $client = Client::getInstance();
        $data = $client->setWebHook($url);
        $this->assertObjectHasAttribute('webhookUrl', $data);
        $this->assertObjectHasAttribute('set', $data);
        $this->assertAttributeEquals($url, 'webhookUrl', $data);
        $this->assertAttributeEquals(true, 'set', $data);
        return $url;
    }

    /**
     * Receive webhook url
     * @depends testSetWebHook
     * @param string $url
     */
    public function testGetWebHook($url) {
        $client = Client::getInstance();
        $data = $client->getWebHook();
        $this->assertObjectHasAttribute('webhookUrl', $data);
        $this->assertObjectHasAttribute('set', $data);
        $this->assertAttributeEquals($url, 'webhookUrl', $data);
        $this->assertAttributeEquals(false, 'set', $data);
    }

    /**
     * Clear not sent messages queue
     */
    public function testClearMessagesQueue() {
        $client = Client::getInstance();
        $data = $client->clearMessagesQueue();
        $this->assertInstanceOf('stdClass', $data);
        $this->assertObjectHasAttribute('message', $data);
        $this->assertObjectHasAttribute('messageTextsExample', $data);
    }

    /**
     * Receive not sent messages queue
     * @depends testClearMessagesQueue
     */
    public function testGetMessagesQueue() {
        $client = Client::getInstance();
        $data = $client->getMessagesQueue();
        $this->assertInstanceOf('stdClass', $data);
        $this->assertObjectHasAttribute('totalMessages', $data);
        $this->assertObjectHasAttribute('first100', $data);
        $this->assertAttributeEquals(0, 'totalMessages', $data);
    }

    /**
     * Reboot application
     */
    public function testReboot() {
        $client = Client::getInstance();
        $data = $client->reboot();
        $this->assertInstanceOf('stdClass', $data);
        $this->assertObjectHasAttribute('success', $data);
        $this->assertAttributeEquals(true, 'success', $data);
    }

    /**
     * File sending
     */
    public function testSendFile() {
        $client = Client::getInstance();
        $name = 'sample.jpg';
        $imgData = base64_encode(file_get_contents($name));
        $src = 'data: '.mime_content_type($name).';base64,'.$imgData;
        $data = $client->sendFile([
            'phone' => $this->phone, 'body' => $src, 'filename' => 'sample.jpg'
        ]);
        $this->assertInstanceOf('stdClass', $data);
        $this->assertObjectHasAttribute('sent', $data);
        $this->assertObjectHasAttribute('message', $data);
        $this->assertAttributeEquals(true, 'sent', $data);
    }
}
