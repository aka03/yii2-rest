<?php

namespace app\models\definitions;

/**
 * @OA\Schema(required={"first_name", "last_name", "patronymic_name", "login", "email", "password"},
 *      @OA\Property(property="id", type="integer", readOnly="true"),
 *      @OA\Property(property="first_name", type="string"),
 *      @OA\Property(property="last_name", type="string"),
 *      @OA\Property(property="patronymic_name", type="string"),
 *      @OA\Property(property="login", type="string"),
 *      @OA\Property(property="password", type="string"),
 *      @OA\Property(property="email", type="string"),
 *      @OA\Property(property="status", type="integer", enum={"0: Blocked", "10: Active"}, default=10),
 *      @OA\Property(property="created_at", type="integer", readOnly="true"),
 *      @OA\Property(property="updated_at", type="integer", readOnly="true"),
 * )
 */
class User
{
}
