<?php
namespace Src\Enums;

enum FuncType
{
    case ByDate;
    case ById;
    case ByTypeId;
    case History;
    case All;
    case None;
}

