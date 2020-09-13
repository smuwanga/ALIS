<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;
class UNHLSEquipmentInventory extends Eloquent
{
	use SoftDeletingTrait;
	protected $dates = ['deleted_at'];
	protected $table = "unhls_equipment_inventory";
}