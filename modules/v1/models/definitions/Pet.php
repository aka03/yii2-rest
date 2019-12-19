<?php

namespace app\models\definitions;

/**
 * @OA\Schema(required={"name", "breed", "category_id", "status", "price", "user_email", "user_phone"},
 *      @OA\Property(property="id", type="integer", readOnly="true"),
 *      @OA\Property(property="user_id", type="integer", readOnly="true"),
 *      @OA\Property(property="name", type="string"),
 *      @OA\Property(property="breed", type="string"),
 *      @OA\Property(property="category_id",type="integer",enum={"See available categories in '/pet-categories'"},example=1),
 *      @OA\Property(property="images", ref="#/components/schemas/PetImage", format="binary", type="object"),
 *      @OA\Property(property="comment", type="string"),
 *      @OA\Property(property="price", type="number", format="float"),
 *      @OA\Property(property="user_email", type="string"),
 *      @OA\Property(property="user_phone", type="string"),
 *      @OA\Property(property="status", type="integer", enum={"0: Sold Out", "10: For Sale"}, default=10),
 *      @OA\Property(property="created_at", type="integer", readOnly="true"),
 *      @OA\Property(property="updated_at", type="integer", readOnly="true"),
 * )
 */
class Pet
{
}
