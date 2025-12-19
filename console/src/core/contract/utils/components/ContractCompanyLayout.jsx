import { Outlet } from 'react-router-dom'
import { ContractCompanyProvider } from '../context/ContractCompanyContext'

export const ContractCompanyLayout = () => {
    return (
        <ContractCompanyProvider>
            <Outlet />
        </ContractCompanyProvider>
    )
}
