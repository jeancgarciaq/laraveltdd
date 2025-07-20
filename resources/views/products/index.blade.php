<!-- resources/views/products/index.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <title>Products</title>
</head>
<body>
    <h1>Products List</h1>
    
    @if(session('success'))
        <div style="color: green;">{{ session('success') }}</div>
    @endif
    
    @if($products->count() > 0)
        <ul>
            @foreach($products as $product)
                <li>
                    <strong>{{ $product->name }}</strong> - ${{ $product->price }}
                    <br>
                    <small>{{ $product->description }}</small>
                    <br>
                    <em>Category: {{ $product->category->name ?? 'N/A' }}</em>
                </li>
            @endforeach
        </ul>
    @else
        <p>No products found.</p>
    @endif
    
    <a href="{{ route('products.create') }}">Create New Product</a>
</body>
</html>