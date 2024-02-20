<x-app-layout>
    <div x-data="{
        flashMessage: '{{ \Illuminate\Support\Facades\Session::get('flash_message') }}',
        init() {
            if (this.flashMessage) {
                setTimeout(() => this.$dispatch('notify', { message: this.flashMessage }), 200)
            }
        }
    }" class="container p-5 mx-auto lg:w-2/3">

        @if (session('error'))
            <div class="bg-red-500 py-2 px-3 text-white mb-2 rounded">
                {{ session('error') }}
            </div>
        @endif
        
        <div class="grid grid-cols-1 md:grid-cols-3 items-start gap-6">
            <div class="bg-white rounded-lg shadow p-3 md:col-span-2">
                <form x-data="{
                    countries: {{ json_encode($countries) }},
                    billingAddress: {{ json_encode([
                        'address1' => old('billing.address1', $billingAddress->address1),
                        'address2' => old('billing.address2', $billingAddress->address2),
                        'city' => old('billing.city', $billingAddress->city),
                        'state' => old('billing.state', $billingAddress->state),
                        'country_code' => old('billing.country_code', $billingAddress->country_code),
                        'zip_code' => old('billing.zip_code', $billingAddress->zip_code),
                    ]) }},
                    shippingAddress: {{ json_encode([
                        'address1' => old('shipping.address1', $shippingAddress->address1),
                        'address2' => old('shipping.address2', $shippingAddress->address2),
                        'city' => old('shipping.city', $shippingAddress->city),
                        'state' => old('shipping.state', $shippingAddress->state),
                        'country_code' => old('shipping.country_code', $shippingAddress->country_code),
                        'zip_code' => old('shipping.zip_code', $shippingAddress->zip_code),
                    ]) }},
                    get billingCountryStates() {
                        const country = this.countries.find((c) => c.code === this.billingAddress.country_code)
                        if (country && country.states) {
                            return JSON.parse(country.states);
                        }
                        return null;
                    },
                    get shippingCountryStates() {
                        const country = this.countries.find((c) => c.code === this.shippingAddress.country_code)
                        if (country && country.states) {
                            return JSON.parse(country.states);
                        }
                        return null;
                    },
                }" action="{{ route('profile.update') }}" method="POST">
                    @csrf
                    <h2 class="text-xl font-semibold mb-2">Profile Details</h2>
                    <div class="grid grid-cols-2 mb-3 gap-3">
                        <x-text-input type="text" name="first_name"
                            value="{{ old('first_name', $customer->first_name) }}" placeholder="First Name"
                            class="rounded w-full focus:border-purple-600 border-gray-300 focus:ring-purple-600" />
                        <x-text-input type="text" name="last_name"
                            value="{{ old('last_name', $customer->last_name) }}" placeholder="Last Name"
                            class="rounded w-full focus:border-purple-600 border-gray-300 focus:ring-purple-600" />
                    </div>

                    <div class="mb-3">
                        <x-text-input
                            class="rounded w-full focus:ring-purple-600 focus:border-purple-600 border-gray-300"
                            type="text" name="email" placeholder="Your Email"
                            value="{{ old('email', $user->email) }}" />
                    </div>

                    <div class="mb-3">
                        <x-text-input
                            class="rounded w-full focus:ring-purple-600 focus:border-purple-600 border-gray-300"
                            type="text" name="phone" placeholder="Your Phone Number"
                            value="{{ old('phone', $customer->phone) }}" />
                    </div>

                    <h2 class="text-xl mt-6 mb-2 font-semibold">Billing Address</h2>
                    <div class="grid grid-cols-2 mb-3 gap-3">
                        <div>
                            <x-text-input
                                class="rounded w-full focus:border-purple-600 focus:ring-purple-600 border-gray-300"
                                name="billing[address1]" placeholder="Address 1" type="text"
                                x-model="billingAddress.address1" />
                        </div>
                        <div>
                            <x-text-input
                                class="rounded w-full focus:border-purple-600 focus:ring-purple-600 border-gray-300"
                                name="billing[address2]" placeholder="Address 2" type="text"
                                x-model="billingAddress.address2" />
                        </div>
                    </div>

                    <div class="grid grid-cols-2 mb-3 gap-3">
                        <div>
                            <x-text-input type="text" placeholder="City" name="billing[city]"
                                class="w-full rounded focus:border-purple-600 focus:ring-purple-600 border-gray-300"
                                x-model="billingAddress.city" />
                        </div>
                        <div>
                            <x-text-input type="text" placeholder="Zip Code" name="billing[zip_code]"
                                class="w-full rounded focus:border-purple-600 focus:ring-purple-600 border-gray-300"
                                x-model="billingAddress.zip_code" />
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3 mb-3">
                        <div>
                            <x-text-input type="select" name="billing[country_code]" x-model="billingAddress.country_code"
                                class="w-full focus:border-purple-600 focus:ring-purple-600 border-gray-300 rounded">
                                <option value="">Select Country</option>
                                <template x-for="country of countries" :key="country.code">
                                    <option :selected="country.code === billingAddress.country_code"
                                        :value="country.code" x-text="country.name"></option>
                                </template>
                            </x-text-input>
                        </div>
                        <div>
                            <template x-if="billingCountryStates">
                                <x-text-input type="select" name="billing[state]" x-model="billingAddress.state"
                                    class="w-full focus:border-purple-600 focus:ring-purple-600 border-gray-300 rounded">
                                    <option value="">Select State</option>
                                    <template x-for="[code, state] of Object.entries(billingCountryStates)"
                                        :key="code">
                                        <option :selected="code === billingAddress.state" :value="code"
                                            x-text="state"></option>
                                    </template>
                                </x-text-input>
                            </template>
                            <template x-if="!billingCountryStates">
                                <x-text-input type="text" name="billing[state]" x-model="billingAddress.state"
                                    placeholder="State"
                                    class="w-full focus:border-purple-600 focus:ring-purple-600 border-gray-300 rounded" />
                            </template>
                        </div>
                    </div>

                    <div class="flex justify-between mt-6 mb-2">
                        <h2 class="text-xl font-semibold">Shipping Address</h2>
                        <label for="sameAsBillingAddress" class="text-gray-700">
                            <input @change="$event.target.checked ? shippingAddress = {...billingAddress} : ''"
                                id="sameAsBillingAddress" type="checkbox"
                                class="text-purple-600 focus:ring-purple-600 mr-2"> Same as Billing
                        </label>
                    </div>

                    <div class="grid grid-cols-2 mb-3 gap-3">
                        <div>
                            <x-text-input
                                class="w-full rounded border-gray-300 focus:border-purple-600 focus:ring-purple-600"
                                placeholder="Address 1" name="shipping[address1]" x-model="shippingAddress.address1"
                                type="text" />
                        </div>
                        <div>
                            <x-text-input
                                class="w-full rounded border-gray-300 focus:border-purple-600 focus:ring-purple-600"
                                placeholder="Address 2" name="shipping[address2]" x-model="shippingAddress.address2"
                                type="text" />
                        </div>
                    </div>

                    <div class="grid grid-cols-2 mb-3 gap-3">
                        <div>
                            <x-text-input
                                class="w-full rounded border-gray-300 focus:ring-purple-600 focus:border-purple-600"
                                type="text" name="shipping[city]" x-model="shippingAddress.city"
                                placeholder="City" />
                        </div>
                        <div>
                            <x-text-input
                                class="w-full rounded border-gray-300 focus:ring-purple-600 focus:border-purple-600"
                                type="text" name="shipping[zip_code]" x-model="shippingAddress.zip_code"
                                placeholder="Zip Code" />
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3 mb-3">
                        <div>
                            <x-text-input type="select" name="shipping[country_code]"
                                x-model="shippingAddress.country_code"
                                class="w-full focus:border-purple-600 focus:ring-purple-600 border-gray-300 rounded">
                                <option value="">Select Country</option>
                                <template x-for="country of countries" :key="country.code">
                                    <option :selected="country.code === shippingAddress.country_code"
                                        :value="country.code" x-text="country.name"></option>
                                </template>
                            </x-text-input>
                        </div>
                        <div>
                            <template x-if="shippingCountryStates">
                                <x-text-input type="select" name="shipping[state]" x-model="shippingAddress.state"
                                    class="w-full focus:border-purple-600 focus:ring-purple-600 border-gray-300 rounded">
                                    <option value="">Select State</option>
                                    <template x-for="[code, state] of Object.entries(shippingCountryStates)"
                                        :key="code">
                                        <option :selected="code === shippingAddress.state" :value="code"
                                            x-text="state"></option>
                                    </template>
                                </x-text-input>
                            </template>
                            <template x-if="!shippingCountryStates">
                                <x-text-input type="text" name="shipping[state]" x-model="shippingAddress.state"
                                    placeholder="State"
                                    class="w-full focus:border-purple-600 focus:ring-purple-600 border-gray-300 rounded" />
                            </template>
                        </div>
                    </div>

                    <x-primary-button class="w-full">Update</x-primary-button>
                </form>
            </div>

            <div class="bg-white p-3 shadow rounded-lg">
                <form action="{{ route('profile.password_update') }}" method="post">
                    @csrf
                    <h2 class="text-xl font-semibold mb-2">Update Password</h2>
                    <div class="mb-3">
                        <x-text-input type="password" name="old_password"
                            class="w-full border-gray-300 focus:border-purple-600 rounded focus:ring-purple-600"
                            placeholder="Your Current Password" />
                    </div>
                    <div class="mb-3">
                        <x-text-input type="password" name="new_password"
                            class="w-full border-gray-300 focus:border-purple-600 rounded focus:ring-purple-600"
                            placeholder="Your New Password" />
                    </div>
                    <div class="mb-3">
                        <x-text-input type="password" name="new_password_confirmation"
                            class="w-full border-gray-300 focus:border-purple-600 rounded focus:ring-purple-600"
                            placeholder="Confirm New Password" />
                    </div>
                    <x-primary-button>Update</x-primary-button>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>
