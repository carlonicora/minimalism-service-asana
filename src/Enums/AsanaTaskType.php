<?php
namespace CarloNicora\Minimalism\Services\Asana\Enums;

enum AsanaTaskType: string
{
    case Task='default_task';
    case Milestone='milestone';
    case Approval='approval';
}