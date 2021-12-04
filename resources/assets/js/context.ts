import React, { useContext } from 'react';

export const MyProfileContext = React.createContext<Tissue.Profile | undefined>(undefined);
MyProfileContext.displayName = 'MyProfileContext';

export const useMyProfile = () => useContext(MyProfileContext);
