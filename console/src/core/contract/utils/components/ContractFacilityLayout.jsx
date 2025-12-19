import { Outlet } from 'react-router-dom'
import { ContractFacilityProvider } from '../context/ContractFacilityContext'

export const ContractFacilityLayout = () => {
    return (
        <ContractFacilityProvider>
            <Outlet />
        </ContractFacilityProvider>
    )
}
