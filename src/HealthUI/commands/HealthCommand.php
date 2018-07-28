<?php

/*
 * HealthUI, a public plugin for PocketMine-MP
 * Copyright (C) 2016-2018 SuperKali
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY;  without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
*/

namespace HealthUI\commands;

use HealthUI\Loader;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\Player;

/**
 * Class HealthCommand
 * @package HealthUI\commands
 */
class HealthCommand extends PluginCommand{

    /** @var Loader $loader */
    private $loader;
    /** @var array $data */
    private $data;

    public function __construct(string $name, Loader $loader)
    {
        parent::__construct($name, $loader);
        $this->loader = $loader;
        $this->setDescription("for heal yourself");
        $this->data = $this->getLoader()->getConfig()->getAll();
    }

    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     * @return bool|mixed
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if ($sender instanceof Player) {
            if ($sender->hasPermission($this->getLoader()->getConfig()->get("permission")) ?? $sender->isOp()) {
                $form = $this->getLoader()->getForm()->createSimpleForm(function (Player $p, array $data) {
                    if ($data[0] !== null) {
                        $value = array_values($this->data["health"]);
                        $health = $value[$data[0]];
                        $p->setHealth(intval($health));
                        $p->sendMessage(Loader::PREFIX . "You have been healed with " . intval($health));
                    }
                });
                $form->setTitle($this->getLoader()->getConfig()->getNested("form.title"));
                $form->setContent($this->getLoader()->getConfig()->getNested("form.content"));
                foreach (array_keys($this->data["health"]) as $key) {
                    $form->addButton($key);
                }
                $form->sendToPlayer($sender);
            }else{
                $sender->sendMessage($this->getLoader()->getConfig()->get("no-permission"));
            }
        }
        return false;
    }

    /**
     * @return Loader
     */
    public function getLoader(): Loader
    {
        return $this->loader;
    }
}
