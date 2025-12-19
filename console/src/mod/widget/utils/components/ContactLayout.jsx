import { Outlet } from 'react-router-dom'
import { ContactProvider } from '../context/ContactContext'

export const ContactLayout = () => {
    return (
        <ContactProvider>
            <Outlet />
        </ContactProvider>
    )
}
