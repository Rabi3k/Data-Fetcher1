<?php
namespace Src\Classes;

class LOrder
{
	public string $storeId;
	public string $order;
	public string $customerId;
	public string $source;
	public string $receiptDate;
	/** @var TotalDiscounts[] */
	public array $totalDiscounts;
	/** @var LineItems[] */
	public array $lineItems;
	public string $note;
	/** @var Payments[] */
	public array $payments;

	/**
	 * @param TotalDiscounts[] $totalDiscounts
	 * @param LineItems[] $lineItems
	 * @param Payments[] $payments
	 */
	public function __construct(
		string $storeId,
		string $order,
		string $customerId,
		string $source,
		string $receiptDate,
		array $totalDiscounts,
		array $lineItems,
		string $note,
		array $payments
	) {
		$this->storeId = $storeId;
		$this->order = $order;
		$this->customerId = $customerId;
		$this->source = $source;
		$this->receiptDate = $receiptDate;
		$this->totalDiscounts = $totalDiscounts;
		$this->lineItems = $lineItems;
		$this->note = $note;
		$this->payments = $payments;
	}
}

class TotalDiscounts
{
	public string $id;
	public ?int $percentage;
	public string $scope;
	public ?int $moneyAmount;

	public function __construct(
		string $id,
		?int $percentage,
		string $scope,
		?int $moneyAmount
	) {
		$this->id = $id;
		$this->percentage = $percentage;
		$this->scope = $scope;
		$this->moneyAmount = $moneyAmount;
	}
}

class LineItems
{
	public string $variantId;
	public int $quantity;
	public ?int $price;
	public ?int $cost;
	public ?string $lineNote;
	/** @var LineDiscounts[]|null */
	public ?array $lineDiscounts;
	/** @var LineTaxes[]|null */
	public ?array $lineTaxes;
	/** @var LineModifiers[]|null */
	public ?array $lineModifiers;

	/**
	 * @param LineDiscounts[]|null $lineDiscounts
	 * @param LineTaxes[]|null $lineTaxes
	 * @param LineModifiers[]|null $lineModifiers
	 */
	public function __construct(
		string $variantId,
		int $quantity,
		?int $price,
		?int $cost,
		?string $lineNote,
		?array $lineDiscounts,
		?array $lineTaxes,
		?array $lineModifiers
	) {
		$this->variantId = $variantId;
		$this->quantity = $quantity;
		$this->price = $price;
		$this->cost = $cost;
		$this->lineNote = $lineNote;
		$this->lineDiscounts = $lineDiscounts;
		$this->lineTaxes = $lineTaxes;
		$this->lineModifiers = $lineModifiers;
	}
}

class LineDiscounts
{
	public string $id;

	public function __construct(string $id)
	{
		$this->id = $id;
	}
}

class LineTaxes
{
	public string $id;

	public function __construct(string $id)
	{
		$this->id = $id;
	}
}

class LineModifiers
{
	public string $modifierOptionId;
	public int $price;

	public function __construct(string $modifierOptionId, int $price)
	{
		$this->modifierOptionId = $modifierOptionId;
		$this->price = $price;
	}
}

class Payments
{
	public string $paymentTypeId;
	public string $paidAt;

	public function __construct(string $paymentTypeId, string $paidAt)
	{
		$this->paymentTypeId = $paymentTypeId;
		$this->paidAt = $paidAt;
	}
}


/*Json input
{
  "store_id": "42dc2cec-6f40-11ea-bde9-1269e7c5a22d",
  "order": "ORDER-103885",
  "customer_id": "c71758a2-79bf-11ea-bde9-1269e7c5a22d",
  "source": "My app",
  "receipt_date": "2020-06-23T08:35:47.047Z",
  "total_discounts": [
    {
      "id": "50f4e245-d221-448d-943a-7346c21cd82b",
      "percentage": 15,
      "scope": "LINE_ITEM"
    },
    {
      "id": "23a64c4f-c6e5-43cc-a017-b11a6ee32448",
      "scope": "RECEIPT"
    },
    {
      "id": "77d2bf40-7b12-11ea-bc55-0242ac130003",
      "money_amount": 5,
      "scope": "RECEIPT"
    }
  ],
  "line_items": [
    {
      "variant_id": "06929667-cc44-4bbb-b226-6758285d7033",
      "quantity": 2
    },
    {
      "variant_id": "706e2626-3329-45f8-98d7-0e1dbcbcb9d9",
      "quantity": 1,
      "price": 100,
      "cost": 50,
      "line_note": "Some line note",
      "line_discounts": [
        {
          "id": "50f4e245-d221-448d-943a-7346c21cd82b"
        }
      ],
      "line_taxes": [
        {
          "id": "a94d8606-7268-11ea-bde9-1269e7c5a22d"
        },
        {
          "id": "365972a1-d5f9-449d-bc61-4328cf0e62cb"
        }
      ],
      "line_modifiers": [
        {
          "modifier_option_id": "0a7cdf41-c75b-11ea-f4ff-abc85f017314",
          "price": 2
        }
      ]
    }
  ],
  "note": "Some note for the receipt",
  "payments": [
    {
      "payment_type_id": "42dd2a55-6f40-11ea-bde9-1269e7c5a22d",
      "paid_at": "2020-06-10T19:16:46Z"
    }
  ]
}*/