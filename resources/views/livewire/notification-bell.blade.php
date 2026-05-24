<div wire:poll.10s="loadNotifications" class="position-relative" x-data>

    {{-- ── Botón campana ── --}}
    <button
        wire:click="toggle"
        class="btn btn-dark position-relative notif-bell-btn"
        style="width:38px; height:38px; padding:0; border-radius:10px; display:flex; align-items:center; justify-content:center;"
        title="Notificaciones"
    >
        <i class="fas fa-bell" style="font-size:.95rem;"></i>

        @if($unreadCount > 0)
            <span class="position-absolute"
                  style="top:-5px; right:-5px; background:#ef4444; color:#fff;
                         border-radius:999px; font-size:.6rem; font-weight:700;
                         min-width:18px; height:18px; display:flex; align-items:center;
                         justify-content:center; padding:0 4px; border:2px solid #212529;">
                {{ $unreadCount > 9 ? '9+' : $unreadCount }}
            </span>
        @endif
    </button>

    {{-- ── Overlay cierra el dropdown ── --}}
    @if($open)
        <div style="position:fixed; inset:0; z-index:1049;" wire:click="close"></div>
    @endif

    {{-- ── Dropdown ── --}}
    @if($open)
    <div class="notif-dropdown shadow-lg"
         style="
            position: absolute;
            top: calc(100% + 10px);
            right: 0;
            width: 360px;
            max-width: 95vw;
            z-index: 1050;
            border-radius: 14px;
            overflow: hidden;
            background: var(--db-card, #fff);
            border: 1px solid var(--db-card-border, #e2e8f0);
         ">

        {{-- Header --}}
        <div style="padding:14px 18px; border-bottom:1px solid var(--db-card-border, #e2e8f0);
                    display:flex; align-items:center; justify-content:space-between;">
            <div style="font-weight:700; font-size:.9rem; color:var(--db-text, #1e293b);">
                <i class="fas fa-bell me-2" style="color:#3b82f6;"></i>
                Notificaciones
                @if($unreadCount > 0)
                    <span style="background:#ef4444; color:#fff; border-radius:999px;
                                 font-size:.65rem; padding:1px 7px; margin-left:6px; font-weight:700;">
                        {{ $unreadCount }}
                    </span>
                @endif
            </div>
            @if($unreadCount > 0)
                <button wire:click="markAllAsRead"
                        style="background:none; border:none; cursor:pointer;
                               font-size:.72rem; color:#64748b; padding:0; font-weight:600;">
                    Marcar todas leídas
                </button>
            @endif
        </div>

        {{-- Lista --}}
        <div style="max-height:420px; overflow-y:auto;">
            @forelse($notifications as $notif)
                <div wire:click="markAsRead({{ $notif->id }})"
                     wire:key="notif-{{ $notif->id }}"
                     style="
                        padding:12px 16px;
                        border-bottom:1px solid var(--db-card-border, #f1f5f9);
                        cursor:pointer;
                        display:flex; gap:12px; align-items:flex-start;
                        transition:background .12s;
                        {{ !$notif->read_at ? 'background:rgba(59,130,246,0.07);' : '' }}
                     "
                     onmouseover="this.style.background='rgba(59,130,246,0.04)'"
                     onmouseout="this.style.background='{{ !$notif->read_at ? 'rgba(59,130,246,0.07)' : 'transparent' }}'">

                    {{-- Icono tipo --}}
                    <div style="
                        width:36px; height:36px; border-radius:10px; flex-shrink:0;
                        display:flex; align-items:center; justify-content:center;
                        background:{{ $notif->icon_color }}1a;
                        color:{{ $notif->icon_color }};
                        font-size:.85rem;
                    ">
                        <i class="fas {{ $notif->icon }}"></i>
                    </div>

                    {{-- Contenido --}}
                    <div style="flex:1; min-width:0;">
                        <div style="font-size:.8rem; font-weight:{{ $notif->read_at ? '500' : '700' }};
                                    color:var(--db-text, #1e293b); margin-bottom:2px;
                                    white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                            {{ $notif->title }}
                        </div>
                        <div style="font-size:.72rem; color:var(--db-muted, #64748b); margin-bottom:3px;
                                    display:-webkit-box; -webkit-line-clamp:2;
                                    -webkit-box-orient:vertical; overflow:hidden;">
                            {{ $notif->body }}
                        </div>
                        <div style="font-size:.68rem; color:#94a3b8;">
                            {{ $notif->created_at->diffForHumans() }}
                        </div>
                    </div>

                    {{-- Punto no leído --}}
                    @if(!$notif->read_at)
                        <div style="width:8px; height:8px; border-radius:50%;
                                    background:#3b82f6; flex-shrink:0; margin-top:5px;"></div>
                    @endif
                </div>

            @empty
                <div style="text-align:center; padding:40px 16px; color:#94a3b8;">
                    <i class="fas fa-bell-slash" style="font-size:2rem; display:block; margin-bottom:10px;"></i>
                    <span style="font-size:.82rem;">Sin notificaciones</span>
                </div>
            @endforelse
        </div>

        {{-- Footer --}}
        @if(count($notifications) >= 12)
            <div style="padding:10px 16px; text-align:center; border-top:1px solid var(--db-card-border, #e2e8f0);">
                <span style="font-size:.72rem; color:#94a3b8;">Mostrando las últimas 12</span>
            </div>
        @endif
    </div>
    @endif
</div>