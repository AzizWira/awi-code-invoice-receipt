<?php

namespace App\Http\Controllers;

use App\Models\ServiceType;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ServiceTypeController extends Controller
{
    public function index()
    {
        $serviceTypes = ServiceType::orderBy('sort_order')->orderBy('name')->get();
        return view('service-types.index', compact('serviceTypes'));
    }

    public function store(Request $request, ActivityLogger $logger)
    {
        $data = $request->validate(['name'=>['required','string','max:100','unique:service_types,name']]);
        $data['sort_order'] = (int) ServiceType::max('sort_order') + 10;
        $service = ServiceType::create($data);
        $logger->log('service_type.created', $service, 'Jenis jasa ditambahkan.');
        return back()->with('success','Jenis jasa berhasil ditambahkan.');
    }

    public function update(Request $request, ServiceType $serviceType, ActivityLogger $logger)
    {
        $data = $request->validate([
            'name'=>['required','string','max:100',Rule::unique('service_types','name')->ignore($serviceType->id)],
            'is_active'=>['nullable','boolean'],
        ]);
        $before = $serviceType->only(['name','is_active']);
        $serviceType->update(['name'=>$data['name'],'is_active'=>$request->boolean('is_active')]);
        $logger->log('service_type.updated', $serviceType, 'Jenis jasa diperbarui.', $before, $serviceType->only(['name','is_active']));
        return back()->with('success','Jenis jasa diperbarui.');
    }

    public function destroy(ServiceType $serviceType, ActivityLogger $logger)
    {
        if (strtolower($serviceType->name) === 'lainnya') return back()->with('error','Jenis jasa Lainnya tidak dapat dihapus.');
        $serviceType->update(['is_active'=>false]);
        $logger->log('service_type.disabled', $serviceType, 'Jenis jasa dinonaktifkan.');
        return back()->with('success','Jenis jasa dinonaktifkan.');
    }
}
