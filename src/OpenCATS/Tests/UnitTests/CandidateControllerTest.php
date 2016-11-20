<?php
use PHPUnit\Framework\TestCase;
use OpenCATS\Controller\CandidateController;
use Symfony\Component\HttpFoundation\Request;

include_once('./lib/UserInterface.php');
include_once('./modules/candidates/CandidatesUI.php');
include_once('./lib/Candidates.php');
include_once('./lib/Template.php');
include_once('./lib/Hooks.php');
include_once('./lib/Session.php');
include_once('./lib/ModuleUtility.php');

class CandidateControllerTest extends TestCase
{
    public function testWhenRequestDisplayIsPopupTemplatePopupIsSet()
    {
        $template = $this->getMockBuilder('\Template')
            ->setMethods(['getLastInsertID'])
            ->getMock();
        $hooks = Hooks::getInstance();
        $map = [
            ['candidateID', null, '1'],
            ['email', null, '']
        ];
        $request = $this->createMock(Request::class);
        $request->method('get')
            ->will($this->returnValueMap(
                $map
            ));
        $_SESSION['CATS'] = new CATSSession();
        $candidates = $this->createMock(\Candidates::class);
        $candidateController = new CandidateController($template, $hooks, $candidates);
        $candidateController->show($request);
        $this->assertTrue($template->popup);
    }

}
