<script context="module">
  import PageLayout from "@appuser-pages/Layouts/AuthenticatedLayout.svelte";
  export const layout = PageLayout;
</script>

<script>
  import { pageTitle } from "@/stores";
  import { toCurrency } from '@/helpers';
  import { inertia } from '@inertiajs/svelte';
  import * as Card from "@/Components/ui/card/index.js";
  import { Badge } from "@/Components/ui/badge/index.js";
  import * as Table from "@/Components/ui/table/index.js";
  import { Button } from "@/Components/ui/button/index.js";
  import ClipboardX from "lucide-svelte/icons/clipboard-x";
  import * as Drawer from "@/Components/ui/drawer/index.js";
  import ArrowUpRight from "lucide-svelte/icons/arrow-up-right";
  import SquareCheckBig from "lucide-svelte/icons/square-check-big";

  export let title = undefined, registrants = [];

  pageTitle.update((t) => title || "User Dashboard");

  let statuses = {
    declined: 'bg-rose-500/10 border-rose-600 text-rose-600',
    pending: 'bg-yellow-500/10 border-yellow-600 text-yellow-600',
    approved: 'bg-emerald-500/10 border-emerald-600 text-emerald-600',
  }
 </script>

<h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight mt-12 ml-8">{title}</h2>

<div class="py-12 transactions">
  <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
      <Card.Root class="xl:col-span-2">
        <Card.Header class="flex flex-row items-center">
          <div class="grid gap-2">
            <Card.Title>Transactions</Card.Title>
            <Card.Description>Recent transactions from your store.</Card.Description>
          </div>
        </Card.Header>
        <Card.Content>
          <Table.Root class="table-auto">
            <Table.Header>
              <Table.Row>
                <Table.Head class="">Customer</Table.Head>
                <Table.Head class="text-right">Amount</Table.Head>
                <Table.Head class="xl:table.-column table-flexible">Status</Table.Head>
                <Table.Head class="xl:table.-column table-flexible">Payment Verified</Table.Head>
                <Table.Head class="xl:table.-column table-flexible">Actions</Table.Head>
              </Table.Row>
            </Table.Header>
            <Table.Body>
              {#each registrants.data as reg}
                 {reg}
              {/each}
              <Table.Row>
                <Table.Cell class="">
                  <div class="font-medium">
                    Liam Johnson
                    <Drawer.Root>
                      <Drawer.Trigger asChild let:builder>
                       <Button builders={[builder]} variant="outline" size="xs">Details</Button>
                      </Drawer.Trigger>
                      <Drawer.Content>
                       <div class="mx-auto w-full max-w-sm text-center">
                        <div class="p-4 pb-0">
                         <div class="h-[120px]">
                          <div class="mx-auto flex w-full max-w-md flex-col overflow-hidden">
                            <h1 class="text-2xl font-medium">John Bunyan</h1>
                            <p class="mt-1 text-sm text-gray-600">xa@y.com / 08034411661</p>
                            <p class="mt-1 text-sm text-gray-600">Reg ID: LAU-54678978675645</p>
                            <p class="mt-1 text-sm text-gray-600"><span class="font-medium">Registered:</span> 2023-06-23</p>
                          </div>
                         </div>
                        </div>
                       </div>
                      </Drawer.Content>
                    </Drawer.Root>
                  </div>

                </Table.Cell>
                <Table.Cell class="text-right">
                  <div class="font-semibold">{ toCurrency(250000) }</div>
                </Table.Cell>
                <Table.Cell class="xl:table.-column table-flexible">
                  <Badge class="text-xs {statuses['approved']}" variant="outline">Approved</Badge>
                </Table.Cell>
                <Table.Cell class="md:table.-cell xl:table.-column table-flexible">2023-06-23</Table.Cell>
                <Table.Cell class="xl:table.-column table-flexible">
                  <Button href={window.route('app.conferences.launch.payment.status')} target="_blank" size="xs" class="ml-auto lg:mr-4 gap-1">
                    View
                    <ArrowUpRight class="h-3 w-3" />
                  </Button>
                  <a href={window.route('conferences.registration.manual-verification', 'conference.registration_id')} use:inertia={{ method:'PUT' }} class="ring-offset-background focus-visible:ring-ring inline-flex items-center justify-center whitespace-nowrap font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-teal-600 text-white hover:bg-teal-600/80 h-6 rounded-sm px-2 py-1 text-xs ml-auto lg:mr-4 gap-1">
                    Verify Payment
                    <SquareCheckBig class="h-3 w-3" />
                  </a>
                  <a href={window.route('app.conferences.launch.payment.status')} class="ring-offset-background focus-visible:ring-ring inline-flex items-center justify-center whitespace-nowrap font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-destructive text-destructive-foreground hover:bg-destructive/90 h-6 rounded-sm px-2 py-1 text-xs ml-auto lg:mr-4 gap-1">
                    Revoke
                    <ClipboardX class="h-3 w-3" />
                  </a>
                </Table.Cell>
              </Table.Row>
              <Table.Row>
                <Table.Cell class="">
                  <div class="font-medium">Olivia Smith</div>
                </Table.Cell>
                <Table.Cell class="text-right">$150.00</Table.Cell>
                <Table.Cell class="xl:table.-column table-flexible">
                  <Badge class="text-xs" variant="outline">Declined</Badge>
                </Table.Cell>
                <Table.Cell class="md:table.-cell xl:table.-column table-flexible">
                  2023-06-24
                </Table.Cell>
                <Table.Cell class="xl:table.-column table-flexible">
                  <Button href="##" size="sm" class="ml-auto gap-1">
                    View All
                    <ArrowUpRight class="h-4 w-4" />
                  </Button>
                </Table.Cell>
              </Table.Row>
            </Table.Body>
          </Table.Root>
        </Card.Content>
      </Card.Root>

    </div>
  </div>
</div>


<style lang="scss">
  :global{
    @media (max-width: 767px) {
      .table-flexible{
        display: table-row !important;
        margin-bottom: 10px;

        &::after, &::before{
          content: '';
          display: block;
          height: 10px;
        }
      }

      table a{
        margin-bottom: 8px;
      }
    }
  }
</style>
