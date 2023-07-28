import React from 'react';
import ReactDOM from "react-dom/client";
import ErrorPage from "./Error";
import HomePage from './HomePage';
import BookPage from './BookPage';
import FriendsPage from './FriendsPage';
import Connexion from './Connexion';
import Header from './Header';
import Footer from './Footer';
import LoginPage from './LoginPage';
import Results from './Results';
import SearchPage from './SearchPage';
import { Link } from "react-router-dom";


import {
    createBrowserRouter,
    RouterProvider,
  } from "react-router-dom";

  export function RequireAuth({ children }) {
    const authed = localStorage.getItem('token') && localStorage.getItem('token') !== 'undefined';
  
    return authed === true ? children : <LoginPage />;
  }

  export default function (props) {
    const router = createBrowserRouter([
      
        {
          path: "/",
          element:<HomePage />,
          errorElement: <ErrorPage />
        },
        {
          path: "/friends",
          element:(
            <>
            <RequireAuth>
              <FriendsPage />
            </RequireAuth>
            </>
          ),
          errorElement: <LoginPage />,
        },
        {
          path: "/books/:bookId",
          element: <BookPage />,
        },
        {
          path: "/connexion",
          element: <LoginPage />,
        },
        {
          path: "/search",
          element: <SearchPage />,
        },
    
      ]);

  return (
    <React.StrictMode>
      <RouterProvider router={router} />
    </React.StrictMode>
  )
}
