/* eslint-disable @typescript-eslint/no-explicit-any */
import type { MapLocation } from '@/hooks/useChat';
import { useEffect, useRef, useState } from 'react';

declare global {
    interface Window {
        google?: any;
        __cekarahMapsPromise?: Promise<void>;
    }
}

const TYPE_LABELS: Record<string, string> = {
    evacuation_shelter: 'Posko Pengungsian',
    public_kitchen: 'Dapur Umum',
    health_post: 'Pos Kesehatan',
    logistics_post: 'Pos Logistik',
};

const API_KEY = import.meta.env.VITE_GOOGLE_MAPS_API_KEY as string | undefined;

function loadGoogleMaps(apiKey: string): Promise<void> {
    if (window.google?.maps) return Promise.resolve();
    if (window.__cekarahMapsPromise) return window.__cekarahMapsPromise;

    window.__cekarahMapsPromise = new Promise((resolve, reject) => {
        const script = document.createElement('script');
        script.src = `https://maps.googleapis.com/maps/api/js?key=${apiKey}`;
        script.async = true;
        script.defer = true;
        script.onload = () => resolve();
        script.onerror = () => reject(new Error('Gagal memuat Google Maps'));
        document.head.appendChild(script);
    });

    return window.__cekarahMapsPromise;
}

function typeLabel(type: string | null): string {
    return (type && TYPE_LABELS[type]) || 'Lokasi';
}

function mapsLink(loc: MapLocation): string {
    return `https://www.google.com/maps/search/?api=1&query=${loc.latitude},${loc.longitude}`;
}

export default function ShelterMap({
    locations,
}: {
    locations: MapLocation[];
}) {
    const mapEl = useRef<HTMLDivElement>(null);
    const mapRef = useRef<any>(null);
    const markersRef = useRef<any[]>([]);
    const infoRef = useRef<any>(null);
    const [active, setActive] = useState(0);
    const [status, setStatus] = useState<'idle' | 'ready' | 'error' | 'nokey'>(
        API_KEY ? 'idle' : 'nokey',
    );

    useEffect(() => {
        if (!API_KEY || !mapEl.current || locations.length === 0) return;

        let cancelled = false;

        loadGoogleMaps(API_KEY)
            .then(() => {
                if (cancelled || !mapEl.current) return;
                const g = window.google;

                const map = new g.maps.Map(mapEl.current, {
                    center: {
                        lat: locations[0].latitude,
                        lng: locations[0].longitude,
                    },
                    zoom: 14,
                    mapTypeControl: false,
                    streetViewControl: false,
                    fullscreenControl: false,
                });
                mapRef.current = map;
                infoRef.current = new g.maps.InfoWindow();

                const bounds = new g.maps.LatLngBounds();
                markersRef.current = locations.map((loc, i) => {
                    const position = { lat: loc.latitude, lng: loc.longitude };
                    bounds.extend(position);
                    const marker = new g.maps.Marker({
                        position,
                        map,
                        title: loc.name,
                    });
                    marker.addListener('click', () => focusMarker(i));
                    return marker;
                });

                if (locations.length > 1) {
                    map.fitBounds(bounds, 48);
                } else {
                    map.setZoom(15);
                }

                setStatus('ready');
            })
            .catch(() => !cancelled && setStatus('error'));

        return () => {
            cancelled = true;
        };
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [locations]);

    const focusMarker = (i: number) => {
        setActive(i);
        const loc = locations[i];
        const marker = markersRef.current[i];
        if (!mapRef.current || !marker || !infoRef.current) return;

        infoRef.current.setContent(
            `<div style="font-family:system-ui;font-size:13px;max-width:220px">
                <strong>${loc.name}</strong><br/>
                <span style="color:#475569">${typeLabel(loc.type)}</span><br/>
                ${loc.address ?? ''}
            </div>`,
        );
        infoRef.current.open(mapRef.current, marker);
        mapRef.current.panTo({ lat: loc.latitude, lng: loc.longitude });
    };

    return (
        <div className="mt-3 overflow-hidden rounded-xl border border-slate-200">
            {status !== 'nokey' && status !== 'error' && (
                <div ref={mapEl} className="h-56 w-full bg-slate-100" />
            )}

            {(status === 'nokey' || status === 'error') && (
                <div className="flex h-20 items-center justify-center bg-slate-50 px-4 text-center text-xs text-slate-500">
                    {status === 'nokey'
                        ? 'Peta interaktif nonaktif (VITE_GOOGLE_MAPS_API_KEY belum diset). Koordinat tetap dapat dibuka di Google Maps di bawah.'
                        : 'Gagal memuat peta. Koordinat tetap dapat dibuka di Google Maps di bawah.'}
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
