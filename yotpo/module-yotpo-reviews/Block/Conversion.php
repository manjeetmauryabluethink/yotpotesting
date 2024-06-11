<?php
namespace Yotpo\Reviews\Block;

use Magento\Checkout\Model\Session;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Sales\Api\OrderRepositoryInterface;
use Yotpo\Reviews\Model\Config as YotpoConfig;

/**
 * Class Conversion
 *
 * Inserts order conversion script in order success page
 */
class Conversion extends Template
{
    /**
     * @var YotpoConfig
     */
    private $yotpoConfig;

    /**
     * @var Session
     */
    private $checkoutSession;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * Conversion constructor.
     * @param Context                   $context
     * @param YotpoConfig               $yotpoConfig
     * @param Session                   $checkoutSession
     * @param OrderRepositoryInterface  $orderRepository
     * @param array<mixed>              $data
     */
    public function __construct(
        Context $context,
        YotpoConfig $yotpoConfig,
        Session $checkoutSession,
        OrderRepositoryInterface $orderRepository,
        array $data = []
    ) {
        $this->yotpoConfig = $yotpoConfig;
        $this->checkoutSession = $checkoutSession;
        $this->orderRepository = $orderRepository;
        parent::__construct($context, $data);
    }

    /**
     * Checks if Yotpo is enabled
     *
     * @return bool
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function isEnabled()
    {
        return $this->yotpoConfig->isEnabled() && $this->yotpoConfig->isAppKeyAndSecretSet();
    }

    /**
     * Get OrderId
     *
     * @return mixed
     */
    public function getOrderId()
    {
        return $this->checkoutSession->getLastOrderId();
    }

    /**
     * Get Order
     *
     * @return array<mixed>|mixed|null
     */
    public function getOrder()
    {
        if (!$this->hasData('order') && $this->getOrderId()) {
            $this->setData('order', $this->orderRepository->get($this->getOrderId()));
        }
        return $this->getData('order');
    }

    /**
     * Check if order exists
     *
     * @return bool
     */
    public function hasOrder()
    {
        return $this->getOrder() && $this->getOrder()->getId();
    }

    /**
     * @return mixed|null
     */
    public function getOrderAmount()
    {
        if (!$this->hasOrder()) {
            return null;
        }
        return $this->getOrder()->getSubtotal();
    }

    /**
     * Get order currency
     *
     * @return mixed |null
     */
    public function getOrderCurrency()
    {
        if (!$this->hasOrder()) {
            return null;
        }
        return $this->getOrder()->getOrderCurrencyCode();
    }

    /**
     * Get json data
     *
     * @return mixed |null
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getJsonData()
    {
        if (!($this->hasOrder() && $this->yotpoConfig->getAppKey())) {
            return null;
        }
        return json_encode(
            [
            "orderId" => $this->getOrder()->getIncrementId(),
            "orderAmount" => $this->getOrderAmount(),
            "orderCurrency" => $this->getOrderCurrency(),
            ]
        );
    }

    /**
     * Get script
     *
     * @return mixed|null
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getNoscriptSrc()
    {
        if (!($this->hasOrder() && $this->yotpoConfig->getAppKey())) {
            return null;
        }
        return $this->yotpoConfig->getYotpoNoSchemaApiUrl(
            "conversion_tracking.gif?" . http_build_query(
                [
                "app_key" => $this->yotpoConfig->getAppKey(),
                "order_id" => $this->getOrderId(),
                "order_amount" => $this->getOrderAmount(),
                "order_currency" => $this->getOrderCurrency(),
                ]
            )
        );
    }

    /**
     * Get widget url
     *
     * @return string
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getYotpoWidgetUrl()
    {
        return $this->yotpoConfig->getYotpoWidgetUrl();
    }
}
