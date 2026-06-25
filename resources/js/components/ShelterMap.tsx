/* eslint-disable @typescript-eslint/no-explicit-any */
import type { MapLocation } from '@/hooks/useChat';
import { useEffect, useRef, useState } from 'react';

declare global {
    interface Window {
        L?: any;
        __cekarahLeafletPromise?: Promise<void>;
    }
}

const TYPE_LABELS: Record<string, string> = {
    evacuation_post: 'Posko Pengungsian',
    field_post: 'Pos Lapangan',
    command_post: 'Pos Koordinasi/Komando',
    national_liaison_post: 'Pos Penghubung Nasional',
    // Legacy labels kept for backward compatibility.
    evacuation_shelter: 'Posko Pengungsian',
    public_kitchen: 'Dapur Umum',
    health_post: 'Pos Kesehatan',
    logistics_post: 'Pos Logistik',
};

const LEAFLET_CSS = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
const LEAFLET_JS = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';

/** Load Leaflet (CSS + JS) from the CDN once, shared across all map instances. */
function loadLeaflet(): Promise<void> {
    if (window.L) return Promise.resolve();
    if (window.__cekarahLeafletPromise) return window.__cekarahLeafletPromise;

    window.__cekarahLeafletPromise = new Promise((resolve, reject) => {
        if (!document.querySelector(`link[href="${LEAFLET_CSS}"]`)) {
            const link = document.createElement('link');
            link.rel = 'stylesheet';
            link.href = LEAFLET_CSS;
            document.head.appendChild(link);
        }

        const script = document.createElement('script');
        script.src = LEAFLET_JS;
        script.async = true;
        script.onload = () => resolve();
        script.onerror = () => reject(new Error('Gagal memuat Leaflet'));
        document.head.appendChild(script);
    });

    return window.__cekarahLeafletPromise;
}

function typeLabel(type: string | null): string {
    return (type && TYPE_LABELS[type]) || 'Lokasi';
}

function mapsLink(loc: MapLocation): string {
    return `https://www.google.com/maps/search/?api=1&query=${loc.latitude},${loc.longitude}`;
}

function numberedIcon(L: any, n: number) {
    return L.divIcon({
        className: '',
        html: `<div style="display:flex;align-items:center;justify-content:center;width:26px;height:26px;border-radius:9999px;background:#3B82F6;color:#fff;font:600 12px system-ui;border:2px solid #fff;box-shadow:0 1px 4px rgba(0,0,0,.3)">${n}</div>`,
        iconSize: [26, 26],
        iconAnchor: [13, 13],
    });
}

export default function ShelterMap({
    locations,
}: {
    locations: MapLocation[];
}) {
    const mapEl = useRef<HTMLDivElement>(null);
    const mapRef = useRef<any>(null);
    const markersRef = useRef<any[]>([]);
    const [active, setActive] = useState(0);
    const [status, setStatus] = useState<'loading' | 'ready' | 'error'>(
        'loading',
    );

    useEffect(() => {
        if (!mapEl.current || locations.length === 0) return;

        let cancelled = false;

        loadLeaflet()
            .then(() => {
                if (cancelled || !mapEl.current || mapRef.current) return;
                const L = window.L;

                const map = L.map(mapEl.current, {
                    scrollWheelZoom: false,
                }).setView([locations[0].latitude, locations[0].longitude], 14);
                mapRef.current = map;

                L.tileLayer(
                    'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
                    {
                        attribution: '&copy; OpenStreetMap',
                        maxZoom: 19,
                    },
                ).addTo(map);

                markersRef.current = locations.map((loc, i) => {
                    const marker = L.marker([loc.latitude, loc.longitude], {
                        icon: numberedIcon(L, i + 1),
                    })
                        .addTo(map)
                        .bindPopup(
                            `<strong>${loc.name}</strong><br/><span style="color:#475569">${typeLabel(loc.type)}</span><br/>${loc.address ?? ''}`,
                        );
                    marker.on('click', () => setActive(i));
                    return marker;
                });

                if (locations.length > 1) {
                    map.fitBounds(
                        L.latLngBounds(
                            locations.map((l) => [l.latitude, l.longitude]),
                        ).pad(0.25),
                    );
                } else {
                    map.setZoom(15);
                }

                setStatus('ready');
            })
            .catch(() => !cancelled && setStatus('error'));

        return () => {
            cancelled = true;
            if (mapRef.current) {
                mapRef.current.remove();
                mapRef.current = null;
            }
        };
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [locations]);

    const focusMarker = (i: number) => {
        setActive(i);
        const loc = locations[i];
        const marker = markersRef.current[i];
        if (!mapRef.current || !marker) return;
        mapRef.current.setView([loc.latitude, loc.longitude], 16);
        marker.openPopup();
    };

    return (
        <div className="mt-3 overflow-hidden rounded-xl border border-slate-200">
            {status !== 'error' && (
                <div ref={mapEl} className="h-56 w-full bg-slate-100" />
            )}

            {status === 'error' && (
                <div className="flex h-20 items-center justify-center bg-slate-50 px-4 text-center text-xs text-slate-500">
                    Gagal memuat peta. Koordinat tetap dapat dibuka di Google
                    Maps di bawah.
                </div>
            )}

            <ul className="divide-y divide-slate-100">
                {locations.map((loc, i) => (
                    <li key={i}>
                        <button
                            onClick={() => status === 'ready' && focusMarker(i)}
                            className={`flex w-full items-start justify-between gap-3 px-3 py-2.5 text-left transition-colors ${
                                active === i && status === 'ready'
                                    ? 'bg-blue-50'
                                    : 'bg-white hover:bg-slate-50'
                            }`}
                        >
                            <div className="min-w-0">
                                <p className="flex items-center gap-2 text-sm font-medium text-slate-800">
                                    <span className="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-blue-100 text-xs font-semibold text-blue-700">
                                        {i + 1}
                                    </span>
                                    {loc.name}
                                </p>
                                <p className="mt-0.5 pl-7 text-xs text-slate-500">
                                    {typeLabel(loc.type)}
                                    {loc.address ? ` · ${loc.address}` : ''}
                                </p>
                                {loc.capacity != null && (
                                    <p className="pl-7 text-xs text-slate-400">
                                        Kapasitas {loc.capacity}
                                        {loc.occupancy != null
                                            ? ` · terisi ${loc.occupancy}`
                                            : ''}
                                    </p>
                                )}
                            </div>
                            <a
                                href={mapsLink(loc)}
                                target="_blank"
                                rel="noopener noreferrer"
                                onClick={(e) => e.stopPropagation()}
                                className="shrink-0 text-xs text-blue-600 hover:underline"
                            >
                                Buka di Maps →
                            </a>
                        </button>
                    </li>
                ))}
            </ul>
        </div>
    );
}
