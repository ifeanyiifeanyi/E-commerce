<?php

namespace App\Services;

class DeviceDetectionService
{
    private $userAgent;
    private $detector;

    public function __construct($userAgent = null)
    {
        $this->userAgent = $userAgent ?? request()->userAgent();
        $this->detector = new DeviceDetector($this->userAgent);
        $this->detector->parse();
    }

    public function getDeviceInfo(): array
    {
        $deviceInfo = $this->detector->getDeviceInfo();

        return [
            'device_type' => $deviceInfo['type'] ?? 'unknown',
            'device_name' => $deviceInfo['brand'] ?? 'unknown',
            'device_model' => $deviceInfo['model'] ?? 'unknown',
        ];
    }

    public function getBrowserInfo(): array
    {
        $client = $this->detector->getClient();

        return [
            'browser' => $client['name'] ?? 'unknown',
            'browser_version' => $client['version'] ?? 'unknown',
        ];
    }

    public function getOsInfo(): array
    {
        $os = $this->detector->getOs();

        return [
            'operating_system' => $os['name'] ?? 'unknown',
            'os_version' => $os['version'] ?? 'unknown',
        ];
    }

    public function getAllInfo(): array
    {
        return array_merge(
            $this->getDeviceInfo(),
            $this->getBrowserInfo(),
            $this->getOsInfo(),
            [
                'user_agent' => $this->userAgent,
                'is_mobile' => $this->detector->isMobile(),
                'is_tablet' => $this->detector->isTablet(),
                'is_desktop' => $this->detector->isDesktop(),
                'is_bot' => $this->detector->isBot(),
            ]
        );
    }

}
