<script context="module">
  import PageLayout from "@appuser-pages/Layouts/AuthenticatedLayout.svelte";
  export const layout = PageLayout;
</script>

<script>
  import { pageTitle } from "@/stores";
  import Users from "lucide-svelte/icons/users";
  import Activity from "lucide-svelte/icons/activity";
  import * as Card from "@/Components/ui/card/index.js";
  import { Badge } from "@/Components/ui/badge/index.js";
  import * as Table from "@/Components/ui/table/index.js";
  import { Button } from "@/Components/ui/button/index.js";
  import CreditCard from "lucide-svelte/icons/credit-card";
  import DollarSign from "lucide-svelte/icons/dollar-sign";
  import * as Avatar from "@/Components/ui/avatar/index.js";
  import ArrowUpRight from "lucide-svelte/icons/arrow-up-right";

  export let title

  pageTitle.update((t) => title || "User Dashboard");
</script>

<template>
  <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight mt-12 ml-8">User Dashboard</h2>

  <div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
      <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
        <main class="flex flex-1 flex-col gap-4 p-4 md:gap-8 md:p-8">
          <div class="grid gap-4 md:grid-cols-2 md:gap-8 lg:grid-cols-4">
            <Card.Root>
              <Card.Header class="flex flex-row items-center justify-between space-y-0 pb-2">
                <Card.Title class="text-sm font-medium">Total Revenue</Card.Title>
                <DollarSign class="text-muted-foreground h-4 w-4" />
              </Card.Header>
              <Card.Content>
                <div class="text-2xl font-bold">$45,231.89</div>
              </Card.Content>
            </Card.Root>
            <Card.Root class="lg:col-start-4">
              <Card.Header class="flex flex-row items-center justify-between space-y-0 pb-2">
                <Card.Title class="text-sm font-medium">Total Subscriptions</Card.Title>
                <Users class="text-muted-foreground h-4 w-4" />
              </Card.Header>
              <Card.Content>
                <div class="text-2xl font-bold">+2350</div>
              </Card.Content>
            </Card.Root>
          </div>
          <div class="grid gap-4 md:gap-8 lg:grid-cols-2 xl:grid-cols-3">
            <Card.Root class="xl:col-span-2">
              <Card.Header class="flex flex-row items-center">
                <div class="grid gap-2">
                  <Card.Title>Transactions</Card.Title>
                  <Card.Description>Recent transactions from your registrations.</Card.Description>
                </div>
                <Button href="##" size="sm" class="ml-auto gap-1">
                  View All
                  <ArrowUpRight class="h-4 w-4" />
                </Button>
              </Card.Header>
              <Card.Content>
                <Table.Root>
                  <Table.Header>
                    <Table.Row>
                      <Table.Head>Customer</Table.Head>
                      <Table.Head class="xl:table.-column hidden">Type</Table.Head>
                      <Table.Head class="xl:table.-column hidden">Status</Table.Head>
                      <Table.Head class="xl:table.-column hidden">Date</Table.Head>
                      <Table.Head class="text-right">Amount</Table.Head>
                    </Table.Row>
                  </Table.Header>
                  <Table.Body>
                    <Table.Row>
                      <Table.Cell>
                        <div class="font-medium">Liam Johnson</div>
                        <div class="text-muted-foreground hidden text-sm md:inline">
                          liam@example.com
                        </div>
                      </Table.Cell>
                      <Table.Cell class="xl:table.-column hidden">Sale</Table.Cell>
                      <Table.Cell class="xl:table.-column hidden">
                        <Badge class="text-xs" variant="outline">Approved</Badge>
                      </Table.Cell>
                      <Table.Cell
                        class="md:table.-cell xl:table.-column hidden lg:hidden"
                      >
                        2023-06-23
                      </Table.Cell>
                      <Table.Cell class="text-right">$250.00</Table.Cell>
                    </Table.Row>
                    <Table.Row>
                      <Table.Cell>
                        <div class="font-medium">Olivia Smith</div>
                        <div class="text-muted-foreground hidden text-sm md:inline">
                          olivia@example.com
                        </div>
                      </Table.Cell>
                      <Table.Cell class="xl:table.-column hidden">Refund</Table.Cell>
                      <Table.Cell class="xl:table.-column hidden">
                        <Badge class="text-xs" variant="outline">Declined</Badge>
                      </Table.Cell>
                      <Table.Cell
                        class="md:table.-cell xl:table.-column hidden lg:hidden"
                      >
                        2023-06-24
                      </Table.Cell>
                      <Table.Cell class="text-right">$150.00</Table.Cell>
                    </Table.Row>
                  </Table.Body>
                </Table.Root>
              </Card.Content>
            </Card.Root>
            <Card.Root>
              <Card.Header>
                <Card.Title>Recent Registrations Summary</Card.Title>
              </Card.Header>
              <Card.Content class="grid gap-8">
                <div class="flex items-center gap-4">
                  <Avatar.Root class="hidden h-9 w-9 sm:flex">
                    <Avatar.Image src="/avatars/01.png" alt="Avatar" />
                    <Avatar.Fallback>OM</Avatar.Fallback>
                  </Avatar.Root>
                  <div class="grid gap-1">
                    <p class="text-sm font-medium leading-none">Olivia Martin</p>
                    <p class="text-muted-foreground text-sm">olivia.martin@email.com</p>
                  </div>
                  <div class="ml-auto font-medium">+$1,999.00</div>
                </div>
                <div class="flex items-center gap-4">
                  <Avatar.Root class="hidden h-9 w-9 sm:flex">
                    <Avatar.Image src="/avatars/02.png" alt="Avatar" />
                    <Avatar.Fallback>JL</Avatar.Fallback>
                  </Avatar.Root>
                  <div class="grid gap-1">
                    <p class="text-sm font-medium leading-none">Jackson Lee</p>
                    <p class="text-muted-foreground text-sm">jackson.lee@email.com</p>
                  </div>
                  <div class="ml-auto font-medium">+$39.00</div>
                </div>
              </Card.Content>
            </Card.Root>
          </div>
        </main>
      </div>
    </div>
  </div>
</template>
