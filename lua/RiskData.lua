local riskdata = {}
local php

--[[
@brief Setup the interface.
--]]

function riskdata.setupInterface( options )
   -- Remove setup function.
   riskdata.setupInterface = nil
   
   -- Copy the PHP callbacks to a local variable, and remove the global one.
   php = mw_interface
   mw_interface = nil
   
   -- Do any other setup here.
   riskdata.select = php.select

   -- Install into the mw global.
   mw = mw or {}
   mw.ext = mw.ext or {}
   mw.ext.riskdata = riskdata
   
   -- Indicate that we're loaded.
   package.loaded['mw.ext.riskdata'] = riskdata
end

return riskdata
