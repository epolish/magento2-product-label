<?php
declare(strict_types=1);

namespace Polish\ProductLabel\Controller\Adminhtml\Image;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Polish\ProductLabel\Model\ProductLabel\ImageUploader;
use Psr\Log\LoggerInterface;

/**
 * Class Upload
 */
class Upload extends Action implements HttpPostActionInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Image uploader
     *
     * @var ImageUploader
     */
    private $imageUploader;

    /**
     * Upload constructor.
     * @param Context $context
     * @param LoggerInterface $logger
     * @param ImageUploader $imageUploader
     */
    public function __construct(
        Context $context,
        LoggerInterface $logger,
        ImageUploader $imageUploader
    ) {
        parent::__construct($context);

        $this->logger = $logger;
        $this->imageUploader = $imageUploader;
    }

    /**
     * Check admin permissions for this controller
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Polish_ProductLabel::edit');
    }

    /**
     * Upload file controller action
     *
     * @return ResultInterface
     */
    public function execute()
    {
        try {
            $result = $this->imageUploader->saveFileToTmpDir(
                (string)$this->getRequest()->getParam('param_name', 'image')
            );
        } catch (Exception $ex) {
            $this->logger->error(sprintf(
                "Message: %s; File: %s; Line: %s; Param: %s;",
                $ex->getMessage(),
                $ex->getFile(),
                $ex->getLine(),
                (string)$this->getRequest()->getParam('param_name', 'image')
            ));

            $result = ['error' => $ex->getMessage(), 'errorcode' => $ex->getCode()];
        }

        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($result);
    }
}
