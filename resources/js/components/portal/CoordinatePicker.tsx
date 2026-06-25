/* eslint-disable @typescript-eslint/no-explicit-any */
import { useEffect, useRef } from 'react';

declare global {
    interface Window {
        L?: any;
        __cekarahLeafletPromise?: Promise<void>;
    }
}

const CSS = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
const JS = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';

function loadLeaflet(): Promise<void> {
    if (window.L) return Promise.resolve();
    if (window.__cekarahLeafletPromise) return window.__cekarahLeafletPromise;
    window.__cekarahLeafletPromise = new Promise((resolve, reject) => {
        if (!document.querySelector(`link[href="${CSS}"]`)) {
            const link = document.createElement('link');
            link.rel = 'stylesheet';
            link.href = CSS;
            document.head.appendChild(link);
        }
        const s = document.createElement('script');
        s.src = JS;
        s.async = true;
        s.onload = () => resolve();
        s.onerror = () => reject(new Error('leaflet'));
        document.head.appendChild(s);
    });
    return window.__cekarahLeafletPromise;
}

/** Small map: click anywhere to drop/move the marker and report coordinates. */
export default function CoordinatePicker({
    lat,
    lng,
    onPick,
}: {
    lat: number | null;
    lng: number | null;
    onPick: (lat: number, lng: number) => void;
}) {
    const el = useRef<HTMLDivElement>(null);
    const mapRef = useRef<any>(null);
    const markerRef = useRef<any>(null);
    const onPickRef = useRef(onPick);
    onPickRef.current = onPick;

    // Indonesia-ish default center when no coordinate yet.
    const startLat = lat ?? 3.6001;
    const startLng = lng ?? 98.4854;

    useEffect(() => {
        let cancelled = false;
        loadLeaflet().then(() => {
            if (cancelled || !el.current || mapRef.current) return;
            const L = window.L;
            const map = L.map(el.current).setView([startLat, startLng], lat ? 14 : 10);
            mapRef.current = map;
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '&copy; OpenStreetMap', maxZoom: 19 }).addTo(map);

            if (lat && lng) markerRef.current = L.marker([lat, lng]).addTo(map);

            map.on('click', (e: any) => {
                const { lat: la, lng: ln } = e.latlng;
                if (markerRef.current) markerRef.current.setLatLng([la, ln]);
                else markerRef.current = L.marker([la, ln]).addTo(map);
                onPickRef.current(Number(la.toFixed(7)), Number(ln.toFixed(7)));
            });
        });
        return () => {
            cancelled = true;
            if (mapRef.current) {
                mapRef.current.remove();
                mapRef.current = null;
            }
        };
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, []);

    // Keep marker in sync when coordinates change via manual inputs.
    useEffect(() => {
        if (!mapRef.current || lat == null || lng == null) return;
        const L = window.L;
        if (markerRef.current) markerRef.current.setLatLng([lat, lng]);
        else if (L) markerRef.current = L.marker([lat, lng]).addTo(mapRef.current);
    }, [lat, lng]);

    return (
        <div className="overflow-hidden rounded-lg border border-slate-200">
            <div ref={el} className="h-48 w-full bg-slate-100" />
            <p className="bg-slate-50 px-3 py-1.5 text-xs text-slate-400">Klik di peta untuk menentukan titik, atau isi koordinat manual di bawah.</p>
        </div>
    );
}
