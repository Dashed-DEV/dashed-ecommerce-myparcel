<?php

namespace Dashed\DashedEcommerceMyParcel\Filament\Pages\Settings;

use Filament\Forms\Get;
use Filament\Pages\Page;
use Filament\Forms\Components\Tabs;
use Dashed\DashedCore\Classes\Sites;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Forms\Components\Placeholder;
use Dashed\DashedCore\Models\Customsetting;
use MyParcelNL\Sdk\src\Model\Carrier\CarrierPostNL;
use Dashed\DashedEcommerceMyParcel\Classes\MyParcel;

class MyParcelSettingsPage extends Page
{
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $title = 'MyParcel';

    protected static string $view = 'dashed-core::settings.pages.default-settings';
    public array $data = [];

    public function mount(): void
    {
        $formData = [];
        $sites = Sites::getSites();
        foreach ($sites as $site) {
            $formData["my_parcel_api_key_{$site['id']}"] = Customsetting::get('my_parcel_api_key', $site['id']);
            $formData["my_parcel_connected_{$site['id']}"] = Customsetting::get('my_parcel_connected', $site['id'], 0) ? true : false;
            $formData["my_parcel_automatically_push_orders_{$site['id']}"] = Customsetting::get('my_parcel_automatically_push_orders', $site['id'], 0) ? true : false;
            $formData["my_parcel_default_package_type_{$site['id']}"] = Customsetting::get('my_parcel_default_package_type', $site['id'], 1);
            $formData["my_parcel_default_delivery_type_{$site['id']}"] = Customsetting::get('my_parcel_default_delivery_type', $site['id'], 2);
            $formData["my_parcel_default_carrier_{$site['id']}"] = Customsetting::get('my_parcel_default_carrier', $site['id'], CarrierPostNL::class);
            $formData["my_parcel_minimum_product_count_{$site['id']}"] = Customsetting::get('my_parcel_minimum_product_count', $site['id'], 2);
            $formData["my_parcel_minimum_product_count_package_type_{$site['id']}"] = Customsetting::get('my_parcel_minimum_product_count_package_type', $site['id'], 1);
        }
        $this->form->fill($formData);
    }

    protected function getFormSchema(): array
    {
        $sites = Sites::getSites();
        $tabGroups = [];

        $tabs = [];
        foreach ($sites as $site) {
            $schema = [
                Placeholder::make('label')
                    ->label("MyParcel voor {$site['name']}")
                    ->content('Activeer MyParcel.')
                    ->columnSpan([
                        'default' => 1,
                        'lg' => 2,
                    ]),
                Placeholder::make('label')
                    ->label("MyParcel is " . (! Customsetting::get('my_parcel_connected', $site['id'], 0) ? 'niet' : '') . ' geconnect')
                    ->content(Customsetting::get('my_parcel_connection_error', $site['id'], ''))
                    ->columnSpan([
                        'default' => 1,
                        'lg' => 2,
                    ]),
                TextInput::make("my_parcel_api_key_{$site['id']}")
                    ->label('MyParcel API key')
                    ->maxLength(255)
                    ->columnSpan([
                        'default' => 1,
                        'lg' => 2,
                    ]),
                Toggle::make("my_parcel_automatically_push_orders_{$site['id']}")
                    ->label('Automatisch bestellingen naar MyParcel pushen')
                    ->reactive()
                    ->helperText('Deze bestellingen komen als concept in MyParcel, pakket type etc kan je nog aanpassen VOORDAT je de label download')
                    ->columnSpan([
                        'default' => 1,
                        'lg' => 2,
                    ]),
                Select::make("my_parcel_default_carrier_{$site['id']}")
                    ->label('Automatische bestelling carrier')
                    ->required(fn (Get $get) => $get("my_parcel_automatically_push_orders_{$site['id']}"))
                    ->reactive()
                    ->options(MyParcel::getCarriers())
                    ->columnSpan([
                        'default' => 1,
                        'lg' => 2,
                    ]),
                Select::make("my_parcel_default_package_type_{$site['id']}")
                    ->label('Automatische bestelling pakket type')
                    ->required(fn (Get $get) => $get("my_parcel_automatically_push_orders_{$site['id']}"))
                    ->reactive()
                    ->options(MyParcel::getPackageTypes())
                    ->helperText('Let op: niet alle opties zijn altijd beschikbaar voor alle adressen')
                    ->columnSpan([
                        'default' => 1,
                        'lg' => 2,
                    ]),
                Select::make("my_parcel_default_delivery_type_{$site['id']}")
                    ->label('Automatisch bestelling verzend type')
                    ->required(fn (Get $get) => $get("my_parcel_automatically_push_orders_{$site['id']}"))
                    ->reactive()
                    ->options(MyParcel::getDeliveryTypes())
                    ->helperText('Let op: niet alle opties zijn altijd beschikbaar voor alle adressen')
                    ->columnSpan([
                        'default' => 1,
                        'lg' => 2,
                    ]),
                TextInput::make("my_parcel_minimum_product_count_{$site['id']}")
                    ->label('Standaard pakket type vanaf een bepaald aantal producten')
                    ->required(fn (Get $get) => $get("my_parcel_automatically_push_orders_{$site['id']}"))
                    ->reactive()
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(1000)
                    ->columnSpan([
                        'default' => 1,
                        'lg' => 2,
                    ]),
                Select::make("my_parcel_minimum_product_count_package_type_{$site['id']}")
                    ->label('Standaard pakket type vanaf een bepaald aantal producten')
                    ->required(fn (Get $get) => $get("my_parcel_automatically_push_orders_{$site['id']}"))
                    ->reactive()
                    ->options(MyParcel::getPackageTypes())
                    ->columnSpan([
                        'default' => 1,
                        'lg' => 2,
                    ]),
            ];

            $tabs[] = Tab::make($site['id'])
                ->label(ucfirst($site['name']))
                ->schema($schema)
                ->columns([
                    'default' => 1,
                    'lg' => 2,
                ]);
        }
        $tabGroups[] = Tabs::make('Sites')
            ->tabs($tabs);

        return $tabGroups;
    }

    public function getFormStatePath(): ?string
    {
        return 'data';
    }

    public function submit()
    {
        $sites = Sites::getSites();

        foreach ($sites as $site) {
            Customsetting::set('my_parcel_api_key', $this->form->getState()["my_parcel_api_key_{$site['id']}"], $site['id']);
            Customsetting::set('my_parcel_automatically_push_orders', $this->form->getState()["my_parcel_automatically_push_orders_{$site['id']}"], $site['id']);
            Customsetting::set('my_parcel_default_package_type', $this->form->getState()["my_parcel_default_package_type_{$site['id']}"], $site['id']);
            Customsetting::set('my_parcel_default_delivery_type', $this->form->getState()["my_parcel_default_delivery_type_{$site['id']}"], $site['id']);
            Customsetting::set('my_parcel_default_carrier', $this->form->getState()["my_parcel_default_carrier_{$site['id']}"], $site['id']);
            Customsetting::set('my_parcel_minimum_product_count', $this->form->getState()["my_parcel_minimum_product_count_{$site['id']}"], $site['id']);
            Customsetting::set('my_parcel_minimum_product_count_package_type', $this->form->getState()["my_parcel_minimum_product_count_package_type_{$site['id']}"], $site['id']);
            Customsetting::set('my_parcel_connected', MyParcel::isConnected($site['id']), $site['id']);
        }

        Notification::make()
            ->title('De MyParcel instellingen zijn opgeslagen')
            ->success()
            ->send();

        return redirect(MyParcelSettingsPage::getUrl());
    }
}
