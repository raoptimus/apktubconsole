<?php
namespace app\components\google;

class Analytics
{
    public function getFirstprofileId(&$analytics) {
        // Get the user's first view (profile) ID.
        // Get the list of accounts for the authorized user.
        $accounts = $analytics->management_accounts->listManagementAccounts();
        $returnList=[];
        if (count($accounts->getItems()) > 0) {
            $itemsParent = $accounts->getItems();
            for ($m = 0; $m < count($itemsParent); $m++) {
                $firstAccountId = $itemsParent[$m]->getId();
                // Get the list of properties for the authorized user.
                $properties = $analytics->management_webproperties
                    ->listManagementWebproperties($firstAccountId);

                if (count($properties->getItems()) > 0) {
                    $itemsList = $properties->getItems();

                    for ($i = 0; $i < count($properties->getItems()); $i++) {
                        $firstPropertyId = $itemsList[$i]->getId();

                        // Get the list of views (profiles) for the authorized user.
                        $profiles = $analytics->management_profiles
                            ->listManagementProfiles($firstAccountId, $firstPropertyId);
                        if (count($profiles->getItems()) > 0) {
                            $items = $profiles->getItems();

                            foreach ($items as $data) {
                               // если не пустой
                                $firstChar = substr($data['websiteUrl'], 0, 1);
                                if ($firstChar !== '-') {
                                    $returnList[] = [
                                        'name'  => $data['websiteUrl'],
                                        'profileId' => $data['id']
                                    ];
                                }
                            }

                        } else {
                            throw new Exception('No views (profiles) found for this user.');
                        }
                    }
                } else {
                    throw new Exception('No properties found for this user.');
                }
        }
        } else {
            throw new Exception('No accounts found for this user.');
        }

        return $returnList;
    }

    public function getAnalytics($accountEmail, $fileP12Key)
    {
        // Create and configure a new client object.
        $client = new \Google_Client();
        $client->setApplicationName("HelloAnalytics");
        $analytics = new \Google_Service_Analytics($client);

        $cred = new \Google_Auth_AssertionCredentials(
            $accountEmail,
            [\Google_Service_Analytics::ANALYTICS_READONLY],
            $fileP12Key
        );

        $client->setAssertionCredentials($cred);

        if($client->getAuth()->isAccessTokenExpired()) {
            $client->getAuth()->refreshTokenWithAssertion($cred);
        }

        return $analytics;
    }
}
