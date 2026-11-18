<x-filament-panels::page>
    <div class="aq-report-wrapper">
        {{-- Top Banner --}}
        <div class="aq-hero-banner" style="padding: 1.15rem 1.5rem;">
            <div class="aq-hero-left">
                <div class="aq-hero-badge">🎨 DYNAMIC BRANDING, FOOTER & THEME ENGINE</div>
                <h2 class="aq-hero-title" style="font-size: 1.2rem;">লোগো, ফেভিকন, কোম্পানির নাম, ফুটার এবং ড্যাশবোর্ড থিম সেটিংস</h2>
                <p class="aq-hero-sub">
                    এখান থেকে ওয়েবসাইট ও অ্যাডমিন প্যানেলের লোগো, ফেভিকন, কোম্পানির নাম, হটলাইন, ফুটার এবং ড্যাশবোর্ড থিম সম্পূর্ণ ডাইনামিকভাবে পরিবর্তন করুন।
                </p>
            </div>
            <div class="aq-hero-actions">
                <a href="{{ url('/') }}" target="_blank" class="aq-qbtn aq-qbtn-glass">
                    <span>🌐 লাইভ ওয়েবসাইট</span>
                </a>
                <a href="{{ url('/admin') }}" class="aq-qbtn aq-qbtn-glass">
                    <span>🏠 ড্যাশবোর্ড</span>
                </a>
                <button type="button" wire:click="resetTheme" class="aq-qbtn aq-qbtn-glass" style="cursor:pointer;">
                    <span>🔄 ডিফল্ট রিসেট</span>
                </button>
                <button type="button" wire:click="saveTheme" class="aq-qbtn aq-qbtn-gold" style="cursor:pointer;">
                    <span>💾 সব সেটিংস সেভ করুন</span>
                </button>
            </div>
        </div>

        <form wire:submit.prevent="saveTheme" style="display: flex; flex-direction: column; gap: 1.25rem;">

            {{-- ========================================================== --}}
            {{-- SECTION A: LOGO, FAVICON & COMPANY NAME (BRANDING)         --}}
            {{-- ========================================================== --}}
            <div class="aq-card">
                <div class="aq-card-header">
                    <div>
                        <h3 class="aq-card-title">🏢 ১. কোম্পানির নাম, লোগো ও ফেভিকন সেটিংস (Company Name, Logo & Favicon)</h3>
                        <span class="aq-card-subtitle">এখানে পরিবর্তন করলে ফ্রন্টএন্ড ওয়েবসাইট এবং অ্যাডমিন প্যানেল—উভয় জায়গায় সাথে সাথে আপডেট হবে</span>
                    </div>
                </div>

                <div style="padding: 1.25rem; display: flex; flex-direction: column; gap: 1.25rem;">
                    {{-- Logo & Favicon Upload Row --}}
                    <div class="aq-section-grid-2">
                        {{-- Site Logo Box --}}
                        <div style="border: 1px dashed #cbd5e1; border-radius: 0.85rem; padding: 1rem; background: #f8fafc; display: flex; align-items: center; gap: 1rem; flex-wrap: wrap;">
                            <div style="width: 76px; height: 76px; border-radius: 0.75rem; background: #ffffff; border: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: center; padding: 0.4rem; flex-shrink: 0; overflow: hidden;">
                                @if($logoUpload)
                                    <img src="{{ $logoUpload->temporaryUrl() }}" alt="Logo Preview" style="max-width: 100%; max-height: 100%; object-fit: contain;" />
                                @else
                                    <img src="{{ $this->currentLogoUrl }}" alt="Current Logo" style="max-width: 100%; max-height: 100%; object-fit: contain;" />
                                @endif
                            </div>

                            <div style="flex: 1; min-width: 200px;">
                                <label style="display: block; font-size: 0.82rem; font-weight: 800; color: #0f172a; margin-bottom: 0.25rem;">
                                    🖼️ ওয়েবসাইট ও অ্যাডমিন লোগো (Site Logo)
                                </label>
                                <p style="font-size: 0.72rem; color: #64748b; margin: 0 0 0.5rem 0;">PNG, JPG, WEBP বা SVG ফরম্যাট (স্বচ্ছ ব্যাকগ্রাউন্ড হলে ভালো)</p>
                                <input type="file" wire:model="logoUpload" accept="image/*" style="font-size: 0.76rem; color: #334155; max-width: 100%;" />
                                <div wire:loading wire:target="logoUpload" style="font-size: 0.72rem; color: #059669; font-weight: 700; margin-top: 0.25rem;">
                                    লোগো আপলোড হচ্ছে...
                                </div>
                                @if(!empty($form['site_logo']))
                                    <button type="button" wire:click="clearCustomLogo" style="margin-top: 0.35rem; font-size: 0.72rem; color: #e11d48; background: none; border: none; cursor: pointer; font-weight: 700; padding: 0;">
                                        ✕ কাস্টম লোগো মুছে ডিফল্ট লোগো রাখুন
                                    </button>
                                @endif
                            </div>
                        </div>

                        {{-- Site Favicon Box --}}
                        <div style="border: 1px dashed #cbd5e1; border-radius: 0.85rem; padding: 1rem; background: #f8fafc; display: flex; align-items: center; gap: 1rem; flex-wrap: wrap;">
                            <div style="width: 76px; height: 76px; border-radius: 0.75rem; background: #ffffff; border: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: center; padding: 0.6rem; flex-shrink: 0; overflow: hidden;">
                                @if($faviconUpload)
                                    <img src="{{ $faviconUpload->temporaryUrl() }}" alt="Favicon Preview" style="width: 42px; height: 42px; object-fit: contain;" />
                                @else
                                    <img src="{{ $this->currentFaviconUrl }}" alt="Current Favicon" style="width: 42px; height: 42px; object-fit: contain;" />
                                @endif
                            </div>

                            <div style="flex: 1; min-width: 200px;">
                                <label style="display: block; font-size: 0.82rem; font-weight: 800; color: #0f172a; margin-bottom: 0.25rem;">
                                    🔖 ব্রাউজার ট্যাব ফেভিকন (Favicon Icon)
                                </label>
                                <p style="font-size: 0.72rem; color: #64748b; margin: 0 0 0.5rem 0;">ব্রাউজারের উপরের ট্যাবে যে ছোট আইকন দেখা যায় (৬৪×৬৪ বা বর্গাকার)</p>
                                <input type="file" wire:model="faviconUpload" accept="image/*,.ico" style="font-size: 0.76rem; color: #334155; max-width: 100%;" />
                                <div wire:loading wire:target="faviconUpload" style="font-size: 0.72rem; color: #059669; font-weight: 700; margin-top: 0.25rem;">
                                    ফেভিকন আপলোড হচ্ছে...
                                </div>
                                @if(!empty($form['site_favicon']))
                                    <button type="button" wire:click="clearCustomFavicon" style="margin-top: 0.35rem; font-size: 0.72rem; color: #e11d48; background: none; border: none; cursor: pointer; font-weight: 700; padding: 0;">
                                        ✕ কাস্টম ফেভিকন মুছে ফেলুন
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Company Name & Contact Inputs --}}
                    <div class="aq-filter-grid">
                        <div class="aq-filter-field">
                            <label>কোম্পানির পূর্ণ নাম (Full Brand / Site Title)</label>
                            <input type="text" wire:model="form.brand_name" class="aq-filter-input" placeholder="Al-Aqsa Mart" />
                        </div>

                        <div class="aq-filter-field">
                            <label>লোগো সাইজ / উচ্চতা (Header Logo Size)</label>
                            <select wire:model="form.logo_height" class="aq-filter-input">
                                <option value="52">৫২px — মিডিয়াম (Medium)</option>
                                <option value="60">৬০px — স্ট্যান্ডার্ড বড় (Standard Large)</option>
                                <option value="68">৬৮px — বড় ও স্পষ্ট (Recommended Default)</option>
                                <option value="78">৭৮px — এক্সট্রা লার্জ (Extra Large)</option>
                                <option value="90">৯০px — সুপার লার্জ (Super Large)</option>
                                <option value="105">১০৫px — ম্যাক্সিমাম বড় (Maximum Large)</option>
                            </select>
                        </div>

                        <div class="aq-filter-field">
                            <label>লোগোর পাশের প্রধান নাম (Logo Main Text)</label>
                            <input type="text" wire:model="form.company_primary_name" class="aq-filter-input" placeholder="AL-AQSA" />
                        </div>

                        <div class="aq-filter-field">
                            <label>লোগোর নিচের সাব-নাম (Logo Sub Text)</label>
                            <input type="text" wire:model="form.company_secondary_name" class="aq-filter-input" placeholder="MART" />
                        </div>

                        <div class="aq-filter-field">
                            <label>কোম্পানির ট্যাগলাইন (Brand Tagline)</label>
                            <input type="text" wire:model="form.company_tagline" class="aq-filter-input" placeholder="Islamic & Halal Shop" />
                        </div>

                        <div class="aq-filter-field">
                            <label>টপবার হটলাইন নম্বর (Topbar Hotline)</label>
                            <input type="text" wire:model="form.topbar_hotline" class="aq-filter-input" placeholder="16297" />
                        </div>

                        <div class="aq-filter-field">
                            <label>হটলাইন সময়সূচি (Support Hours)</label>
                            <input type="text" wire:model="form.topbar_hours" class="aq-filter-input" placeholder="সকাল ৯:০০ - রাত ১১:০০" />
                        </div>

                        <div class="aq-filter-field">
                            <label>হেডার ও ফুটার সাপোর্ট মোবাইল নম্বর</label>
                            <input type="text" wire:model="form.support_phone" class="aq-filter-input" placeholder="01700-000000" />
                        </div>

                        <div class="aq-filter-field">
                            <label>টপবার ডানপাশের স্লোগান (Topbar Slogan)</label>
                            <input type="text" wire:model="form.topbar_slogan" class="aq-filter-input" placeholder="🌙 আল-আকসা ইসলামিক উদ্যোগ" />
                        </div>
                    </div>
                </div>
            </div>

            {{-- ========================================================== --}}
            {{-- SECTION B: DYNAMIC FOOTER SETTINGS                         --}}
            {{-- ========================================================== --}}
            <div class="aq-card">
                <div class="aq-card-header">
                    <div>
                        <h3 class="aq-card-title">🦶 ২. ডাইনামিক ফুটার সেটিংস (Dynamic Footer & Contact Customization)</h3>
                        <span class="aq-card-subtitle">ওয়েবসাইটের নিচের ৪-কলাম ফুটারের বিবরণ, ঠিকানা, পেমেন্ট মেথড, সোশ্যাল লিংক ও কপিরাইট টেক্সট পরিবর্তন করুন</span>
                    </div>
                </div>

                <div style="padding: 1.25rem; display: flex; flex-direction: column; gap: 1rem;">
                    <div class="aq-filter-field">
                        <label>ফুটারের কোম্পানি সংক্ষিপ্ত বিবরণ (Footer About Text)</label>
                        <textarea wire:model="form.footer_description" rows="2" class="aq-filter-input" style="height: auto !important; padding: 0.6rem 0.75rem !important;"></textarea>
                    </div>

                    <div class="aq-filter-grid">
                        <div class="aq-filter-field">
                            <label>ফুটারের ঠিকানা (Company Address)</label>
                            <input type="text" wire:model="form.company_address" class="aq-filter-input" placeholder="ঢাকা, বাংলাদেশ" />
                        </div>

                        <div class="aq-filter-field">
                            <label>সাপোর্ট ইমেইল (Support Email)</label>
                            <input type="email" wire:model="form.support_email" class="aq-filter-input" placeholder="support@alaqsamart.com" />
                        </div>

                        <div class="aq-filter-field">
                            <label>ফুটার ব্যাকগ্রাউন্ড কালার (Footer BG Color)</label>
                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                <input type="color" wire:model="form.footer_bg_color" style="width: 42px; height: 38px; border: 1px solid #cbd5e1; border-radius: 6px; cursor: pointer; padding: 2px; background: #fff;" />
                                <input type="text" wire:model="form.footer_bg_color" class="aq-filter-input" />
                            </div>
                        </div>

                        <div class="aq-filter-field">
                            <label>গ্রহণযোগ্য পেমেন্ট মাধ্যম (কমা দিয়ে লিখুন)</label>
                            <input type="text" wire:model="form.footer_payment_methods" class="aq-filter-input" placeholder="Cash on Delivery, bKash, Nagad, Rocket" />
                        </div>

                        <div class="aq-filter-field">
                            <label>ফুটার ট্রাস্ট ব্যাজ ১ (Badge 1)</label>
                            <input type="text" wire:model="form.footer_badge_1" class="aq-filter-input" placeholder="✓ ১০০% হালাল ও খাঁটি" />
                        </div>

                        <div class="aq-filter-field">
                            <label>ফুটার ট্রাস্ট ব্যাজ ২ (Badge 2)</label>
                            <input type="text" wire:model="form.footer_badge_2" class="aq-filter-input" placeholder="✓ ক্যাশ অন ডেলিভারি" />
                        </div>

                        <div class="aq-filter-field">
                            <label>কপিরাইট টেক্সট (Copyright Text)</label>
                            <input type="text" wire:model="form.footer_copyright" class="aq-filter-input" placeholder="© 2026 Al-Aqsa Mart. সর্বস্বত্ব সংরক্ষিত।" />
                        </div>

                        <div class="aq-filter-field">
                            <label>ফুটার বটম ডানপাশের স্লোগান (Bottom Tagline)</label>
                            <input type="text" wire:model="form.footer_bottom_tagline" class="aq-filter-input" placeholder="🌙 বিশ্বস্ততা ও হালাল বাণিজ্যের অঙ্গীকার" />
                        </div>

                        <div class="aq-filter-field">
                            <label>Facebook Page লিংক (ঐচ্ছিক)</label>
                            <input type="text" wire:model="form.facebook_url" class="aq-filter-input" placeholder="https://facebook.com/..." />
                        </div>

                        <div class="aq-filter-field">
                            <label>YouTube Channel লিংক (ঐচ্ছিক)</label>
                            <input type="text" wire:model="form.youtube_url" class="aq-filter-input" placeholder="https://youtube.com/..." />
                        </div>

                        <div class="aq-filter-field">
                            <label>WhatsApp নম্বর (ঐচ্ছিক)</label>
                            <input type="text" wire:model="form.whatsapp_number" class="aq-filter-input" placeholder="8801700000000" />
                        </div>
                    </div>
                </div>
            </div>

            {{-- ========================================================== --}}
            {{-- SECTION C: 1-CLICK ADMIN DASHBOARD THEME PRESETS           --}}
            {{-- ========================================================== --}}
            <div class="aq-card">
                <div class="aq-card-header">
                    <div>
                        <h3 class="aq-card-title">🎨 ৩. রেডিমেড ড্যাশবোর্ড প্রিসেট থিম (1-Click Admin Theme Presets)</h3>
                        <span class="aq-card-subtitle">যেকোনো একটি থিম কার্ডে ক্লিক করলে সাথে সাথে সম্পূর্ণ অ্যাডমিন প্যানেলের রঙ ও থিম পরিবর্তন হয়ে যাবে</span>
                    </div>
                </div>
                <div style="padding: 1.25rem; display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem;">
                    @foreach($this->presets as $key => $preset)
                        @php
                            $isActive = ($form['preset'] ?? 'emerald') === $key;
                        @endphp
                        <button
                            type="button"
                            wire:click="applyPreset('{{ $key }}')"
                            style="
                                text-align: left;
                                padding: 1rem;
                                border-radius: 0.85rem;
                                border: 2px solid {{ $isActive ? $preset['primary_color'] : '#e2e8f0' }};
                                background: {{ $isActive ? '#f8fafc' : '#ffffff' }};
                                cursor: pointer;
                                transition: all 0.18s ease;
                                display: flex;
                                flex-direction: column;
                                gap: 0.75rem;
                                box-shadow: {{ $isActive ? '0 8px 20px -4px rgba(0,0,0,0.08)' : 'none' }};
                            "
                        >
                            <div style="display: flex; align-items: center; justify-content: space-between; gap: 0.5rem;">
                                <span style="font-size: 0.88rem; font-weight: 800; color: #0f172a;">{{ $preset['name'] }}</span>
                                @if($isActive)
                                    <span class="aq-badge aq-badge-success">✓ সক্রিয়</span>
                                @endif
                            </div>

                            <p style="font-size: 0.75rem; color: #64748b; margin: 0;">{{ $preset['desc'] }}</p>

                            {{-- Color Swatches Preview --}}
                            <div style="display: flex; align-items: center; gap: 0.45rem; margin-top: 0.2rem;">
                                <span title="Sidebar Color" style="width: 28px; height: 28px; border-radius: 8px; background: {{ $preset['sidebar_bg'] }}; border: 1px solid #cbd5e1; display: inline-block;"></span>
                                <span title="Hero Banner" style="width: 28px; height: 28px; border-radius: 8px; background: {{ $preset['hero_bg_end'] }}; border: 1px solid #cbd5e1; display: inline-block;"></span>
                                <span title="Primary Accent" style="width: 28px; height: 28px; border-radius: 8px; background: {{ $preset['primary_color'] }}; border: 1px solid #cbd5e1; display: inline-block;"></span>
                                <span title="Secondary Highlight" style="width: 28px; height: 28px; border-radius: 8px; background: {{ $preset['secondary_color'] }}; border: 1px solid #cbd5e1; display: inline-block;"></span>
                                <span title="Body Background" style="width: 28px; height: 28px; border-radius: 8px; background: {{ $preset['body_bg'] }}; border: 1px solid #cbd5e1; display: inline-block;"></span>
                            </div>
                        </button>
                    @endforeach
                </div>
            </div>

            <div class="aq-section-grid-2">
                {{-- 4. Custom Color Pickers --}}
                <div class="aq-card">
                    <div class="aq-card-header">
                        <div>
                            <h3 class="aq-card-title">৪. কাস্টম কালার কন্ট্রোল (Custom Colors)</h3>
                            <span class="aq-card-subtitle">আপনার পছন্দমতো যেকোনো রঙ বেছে নিন</span>
                        </div>
                    </div>
                    <div style="padding: 1.25rem; display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 1rem;">
                        <div class="aq-filter-field">
                            <label>সাইডবার স্টাইল (Sidebar Mode)</label>
                            <select wire:model="form.sidebar_style" class="aq-filter-input">
                                <option value="dark">🌙 ডার্ক সাইডবার (Dark Sidebar)</option>
                                <option value="light">☀️ লাইট সাইডবার (Light Sidebar)</option>
                            </select>
                        </div>

                        <div class="aq-filter-field">
                            <label>সাইডবার প্রধান কালার (Sidebar BG)</label>
                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                <input type="color" wire:model="form.sidebar_bg" style="width: 42px; height: 38px; border: 1px solid #cbd5e1; border-radius: 6px; cursor: pointer; padding: 2px; background: #fff;" />
                                <input type="text" wire:model="form.sidebar_bg" class="aq-filter-input" />
                            </div>
                        </div>

                        <div class="aq-filter-field">
                            <label>সাইডবার নিচের গ্র্যাডিয়েন্ট (Sidebar End)</label>
                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                <input type="color" wire:model="form.sidebar_bg_end" style="width: 42px; height: 38px; border: 1px solid #cbd5e1; border-radius: 6px; cursor: pointer; padding: 2px; background: #fff;" />
                                <input type="text" wire:model="form.sidebar_bg_end" class="aq-filter-input" />
                            </div>
                        </div>

                        <div class="aq-filter-field">
                            <label>সাইডবার অ্যাক্টিভ মেনু কালার (Active Color)</label>
                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                <input type="color" wire:model="form.sidebar_active_color" style="width: 42px; height: 38px; border: 1px solid #cbd5e1; border-radius: 6px; cursor: pointer; padding: 2px; background: #fff;" />
                                <input type="text" wire:model="form.sidebar_active_color" class="aq-filter-input" />
                            </div>
                        </div>

                        <div class="aq-filter-field">
                            <label>প্রাইমারি অ্যাকসেন্ট কালার (Primary Color)</label>
                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                <input type="color" wire:model="form.primary_color" style="width: 42px; height: 38px; border: 1px solid #cbd5e1; border-radius: 6px; cursor: pointer; padding: 2px; background: #fff;" />
                                <input type="text" wire:model="form.primary_color" class="aq-filter-input" />
                            </div>
                        </div>

                        <div class="aq-filter-field">
                            <label>সেকেন্ডারি / বাটন হাইলাইট কালার</label>
                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                <input type="color" wire:model="form.secondary_color" style="width: 42px; height: 38px; border: 1px solid #cbd5e1; border-radius: 6px; cursor: pointer; padding: 2px; background: #fff;" />
                                <input type="text" wire:model="form.secondary_color" class="aq-filter-input" />
                            </div>
                        </div>

                        <div class="aq-filter-field">
                            <label>হিরো ব্যানার শুরুর কালার (Hero Start)</label>
                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                <input type="color" wire:model="form.hero_bg_start" style="width: 42px; height: 38px; border: 1px solid #cbd5e1; border-radius: 6px; cursor: pointer; padding: 2px; background: #fff;" />
                                <input type="text" wire:model="form.hero_bg_start" class="aq-filter-input" />
                            </div>
                        </div>

                        <div class="aq-filter-field">
                            <label>হিরো ব্যানার শেষের কালার (Hero End)</label>
                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                <input type="color" wire:model="form.hero_bg_end" style="width: 42px; height: 38px; border: 1px solid #cbd5e1; border-radius: 6px; cursor: pointer; padding: 2px; background: #fff;" />
                                <input type="text" wire:model="form.hero_bg_end" class="aq-filter-input" />
                            </div>
                        </div>

                        <div class="aq-filter-field" style="grid-column: span 2;">
                            <label>ড্যাশবোর্ড পেজ ব্যাকগ্রাউন্ড কালার (Page Body Background)</label>
                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                <input type="color" wire:model="form.body_bg" style="width: 42px; height: 38px; border: 1px solid #cbd5e1; border-radius: 6px; cursor: pointer; padding: 2px; background: #fff;" />
                                <input type="text" wire:model="form.body_bg" class="aq-filter-input" />
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 5. Card Design, Grid Columns & Layout Controls --}}
                <div class="aq-card">
                    <div class="aq-card-header">
                        <div>
                            <h3 class="aq-card-title">৫. ড্যাশবোর্ড কার্ড স্টাইল ও কলাম কাস্টমাইজেশন</h3>
                            <span class="aq-card-subtitle">ড্যাশবোর্ডের KPI কার্ডগুলোর ডিজাইন ও কলাম সংখ্যা নির্ধারণ করুন</span>
                        </div>
                    </div>
                    <div style="padding: 1.25rem; display: flex; flex-direction: column; gap: 1rem;">
                        <div class="aq-filter-field">
                            <label>KPI কার্ডের ডিজাইন স্টাইল (Card Visual Style)</label>
                            <select wire:model="form.kpi_card_style" class="aq-filter-input">
                                <option value="modern_white">✨ Modern Smart White (সাদা কার্ড + টপ গ্র্যাডিয়েন্ট বর্ডার ও সফট আইকন)</option>
                                <option value="gradient_colorful">🎨 Full Gradient Colorful (সম্পূর্ণ রঙিন গ্র্যাডিয়েন্ট কার্ড)</option>
                                <option value="minimal_outline">🔲 Clean Minimal Outline (মিনিমাল ক্লিন বর্ডার কার্ড)</option>
                            </select>
                        </div>

                        <div style="display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 1rem;">
                            <div class="aq-filter-field">
                                <label>প্রতি সারিতে কয়টি কার্ড? (Grid Columns)</label>
                                <select wire:model="form.kpi_columns" class="aq-filter-input">
                                    <option value="2">২ কলাম (2 Columns - বড় কার্ড)</option>
                                    <option value="3">৩ কলাম (3 Columns - স্ট্যান্ডার্ড স্মার্ট)</option>
                                    <option value="4">৪ কলাম (4 Columns - কম্প্যাক্ট)</option>
                                </select>
                            </div>

                            <div class="aq-filter-field">
                                <label>কার্ডের কোণার রাউন্ডনেস (Border Radius)</label>
                                <select wire:model="form.card_radius" class="aq-filter-input">
                                    <option value="8px">Sharp (8px)</option>
                                    <option value="12px">Balanced (12px)</option>
                                    <option value="16px">Modern Rounded (16px)</option>
                                    <option value="22px">Extra Smooth (22px)</option>
                                </select>
                            </div>
                        </div>

                        <div class="aq-filter-field">
                            <label>হিরো ব্যানার ব্যাজ টেক্সট (Banner Badge)</label>
                            <input type="text" wire:model="form.welcome_badge" class="aq-filter-input" placeholder="⚡ LIVE STORE COMMAND CENTER" />
                        </div>

                        <div class="aq-filter-field">
                            <label>কাস্টম ওয়েলকাম টাইটেল (ফাঁকা রাখলে স্বয়ংক্রিয়ভাবে ইউজারের নাম দেখাবে)</label>
                            <input type="text" wire:model="form.welcome_title" class="aq-filter-input" placeholder="স্বাগতম, আল-আকসা মার্ট ড্যাশবোর্ডে!" />
                        </div>

                        <div class="aq-filter-field">
                            <label>হিরো ব্যানার সাবটাইটেল</label>
                            <input type="text" wire:model="form.welcome_subtitle" class="aq-filter-input" />
                        </div>
                    </div>
                </div>
            </div>

            {{-- 6. Show / Hide Dashboard Sections & Widgets --}}
            <div class="aq-card">
                <div class="aq-card-header">
                    <div>
                        <h3 class="aq-card-title">৬. ড্যাশবোর্ড উইজেট ও সেকশন নিয়ন্ত্রণ (Show / Hide Widgets)</h3>
                        <span class="aq-card-subtitle">ড্যাশবোর্ডে কোন কোন অংশ দেখাতে চান তা টিক (✓) দিয়ে চালু বা বন্ধ করুন</span>
                    </div>
                </div>
                <div style="padding: 1.25rem; display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 0.9rem;">
                    <label style="display: flex; align-items: center; gap: 0.65rem; padding: 0.75rem 1rem; border-radius: 0.75rem; border: 1px solid #e2e8f0; background: #f8fafc; cursor: pointer;">
                        <input type="checkbox" wire:model="form.show_welcome_banner" style="width: 18px; height: 18px; accent-color: #059669;" />
                        <div>
                            <div style="font-size: 0.82rem; font-weight: 700; color: #0f172a;">👋 স্বাগতম হিরো ব্যানার</div>
                            <div style="font-size: 0.7rem; color: #64748b;">ড্যাশবোর্ডের উপরের কমান্ড ব্যানার</div>
                        </div>
                    </label>

                    <label style="display: flex; align-items: center; gap: 0.65rem; padding: 0.75rem 1rem; border-radius: 0.75rem; border: 1px solid #e2e8f0; background: #f8fafc; cursor: pointer;">
                        <input type="checkbox" wire:model="form.show_quick_actions" style="width: 18px; height: 18px; accent-color: #059669;" />
                        <div>
                            <div style="font-size: 0.82rem; font-weight: 700; color: #0f172a;">⚡ কুইক অ্যাকশন শর্টকাট বাটন</div>
                            <div style="font-size: 0.7rem; color: #64748b;">নতুন প্রোডাক্ট, অর্ডার, রিপোর্ট শর্টকাট</div>
                        </div>
                    </label>

                    <label style="display: flex; align-items: center; gap: 0.65rem; padding: 0.75rem 1rem; border-radius: 0.75rem; border: 1px solid #e2e8f0; background: #f8fafc; cursor: pointer;">
                        <input type="checkbox" wire:model="form.show_kpi_cards" style="width: 18px; height: 18px; accent-color: #059669;" />
                        <div>
                            <div style="font-size: 0.82rem; font-weight: 700; color: #0f172a;">📊 ৬টি স্মার্ট KPI সামারি কার্ড</div>
                            <div style="font-size: 0.7rem; color: #64748b;">বিক্রয়, মুনাফা, অর্ডার, স্টক ও খরচ কার্ড</div>
                        </div>
                    </label>

                    <label style="display: flex; align-items: center; gap: 0.65rem; padding: 0.75rem 1rem; border-radius: 0.75rem; border: 1px solid #e2e8f0; background: #f8fafc; cursor: pointer;">
                        <input type="checkbox" wire:model="form.show_pipeline_bar" style="width: 18px; height: 18px; accent-color: #059669;" />
                        <div>
                            <div style="font-size: 0.82rem; font-weight: 700; color: #0f172a;">🚚 অর্ডার ট্র্যাকিং পাইপলাইন বার</div>
                            <div style="font-size: 0.7rem; color: #64748b;">Pending থেকে Delivered কুইক ফিল্টার বার</div>
                        </div>
                    </label>

                    <label style="display: flex; align-items: center; gap: 0.65rem; padding: 0.75rem 1rem; border-radius: 0.75rem; border: 1px solid #e2e8f0; background: #f8fafc; cursor: pointer;">
                        <input type="checkbox" wire:model="form.show_sales_chart" style="width: 18px; height: 18px; accent-color: #059669;" />
                        <div>
                            <div style="font-size: 0.82rem; font-weight: 700; color: #0f172a;">📈 ৭ দিনের বিক্রয় ও অর্ডার গ্রাফ</div>
                            <div style="font-size: 0.7rem; color: #64748b;">Sales & Order Trend Line Chart</div>
                        </div>
                    </label>

                    <label style="display: flex; align-items: center; gap: 0.65rem; padding: 0.75rem 1rem; border-radius: 0.75rem; border: 1px solid #e2e8f0; background: #f8fafc; cursor: pointer;">
                        <input type="checkbox" wire:model="form.show_order_chart" style="width: 18px; height: 18px; accent-color: #059669;" />
                        <div>
                            <div style="font-size: 0.82rem; font-weight: 700; color: #0f172a;">🍩 অর্ডার স্ট্যাটাস ডোনাট চার্ট</div>
                            <div style="font-size: 0.7rem; color: #64748b;">Order Distribution Doughnut Chart</div>
                        </div>
                    </label>

                    <label style="display: flex; align-items: center; gap: 0.65rem; padding: 0.75rem 1rem; border-radius: 0.75rem; border: 1px solid #e2e8f0; background: #f8fafc; cursor: pointer;">
                        <input type="checkbox" wire:model="form.show_recent_orders" style="width: 18px; height: 18px; accent-color: #059669;" />
                        <div>
                            <div style="font-size: 0.82rem; font-weight: 700; color: #0f172a;">🛒 সর্বশেষ অর্ডারসমূহ টেবিল</div>
                            <div style="font-size: 0.7rem; color: #64748b;">ড্যাশবোর্ডের নিচের সাম্প্রতিক অর্ডার তালিকা</div>
                        </div>
                    </label>

                    <label style="display: flex; align-items: center; gap: 0.65rem; padding: 0.75rem 1rem; border-radius: 0.75rem; border: 1px solid #e2e8f0; background: #f8fafc; cursor: pointer;">
                        <input type="checkbox" wire:model="form.show_topbar_clock" style="width: 18px; height: 18px; accent-color: #059669;" />
                        <div>
                            <div style="font-size: 0.82rem; font-weight: 700; color: #0f172a;">⏰ টপবার লাইভ ঘড়ি ও তারিখ</div>
                            <div style="font-size: 0.7rem; color: #64748b;">উপরের বারে রিয়েল-টাইম ঘড়ি ও ক্যালেন্ডার</div>
                        </div>
                    </label>
                </div>

                <div style="padding: 1rem 1.25rem; background: #f8fafc; border-top: 1px solid #e2e8f0; display: flex; justify-content: flex-end; gap: 0.75rem;">
                    <button type="button" wire:click="resetTheme" class="aq-btn aq-btn-preset">
                        🔄 ডিফল্টে ফিরে যান
                    </button>
                    <button type="submit" class="aq-btn aq-btn-pdf" style="padding: 0.6rem 1.35rem; font-size: 0.85rem;">
                        💾 সব সেটিংস সেভ করুন
                    </button>
                </div>
            </div>
        </form>
    </div>
</x-filament-panels::page>
