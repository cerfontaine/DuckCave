import {BrowserRouter, Routes, Route, Link, useNavigate, Navigate} from 'react-router-dom';
import Home from './components/Home';
import Footer from './components/Footer';
import Header from './components/Header';
import NotFound from './components/NotFound';
import Success from './components/Success';
import Order from './components/Orders';
import Cart from './components/Cart';


function App() {
  return (
      <BrowserRouter>
      <Header />
        <Routes>
            <Route index element={<Home />} />
            <Route path="success" element={<Success />} />
            <Route path="orders" element={<Order />} />
            <Route path="cart" element={<Cart />} />
            <Route path='*' element={<NotFound />}/>
        </Routes>
      <Footer />
      </BrowserRouter>
  );
}

export default App;